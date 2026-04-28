<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class RoyaleExceptFirstClassCallable
{

	public function methodA(): void
	{
		$fn = crc32(...); // disallowed: in except zone, null args can't satisfy allowParamsInAllowed
		$fn = strtolower(...); // allowed: in except zone, null args can't trigger allowExceptParamsInAllowed
	}


	public function methodB(): void
	{
		$fn = crc32(...); // allowed: not in except zone
		$fn = strtolower(...); // allowed: not in except zone
	}

}
