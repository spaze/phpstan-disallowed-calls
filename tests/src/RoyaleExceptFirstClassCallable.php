<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class RoyaleExceptFirstClassCallable
{

	public function methodA(): void
	{
		$fn = crc32(...);  // should be disallowed: in except zone, null args incorrectly satisfy allowParamsInAllowed
	}


	public function methodB(): void
	{
		$fn = crc32(...);  // allowed: not in except zone
	}

}
