<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class RoyaleAllowInFirstClassCallable
{

	public function methodA(): void
	{
		$fn = crc32(...); // disallowed: in allowed zone, null args can't satisfy allowParamsInAllowed
		$fn = strtolower(...); // allowed: in allowed zone, null args can't trigger allowExceptParamsInAllowed
	}


	public function methodB(): void
	{
		$fn = crc32(...); // disallowed: not in allowed zone
		$fn = strtolower(...); // disallowed: not in allowed zone
	}

}
