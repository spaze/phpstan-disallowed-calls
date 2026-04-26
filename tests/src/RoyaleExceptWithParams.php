<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class RoyaleExceptWithParams
{

	public function methodA(): void
	{
		crc32('a');       // allowed: in except zone, param matches allowParamsInAllowed
		crc32('b');       // disallowed: in except zone, param doesn't match allowParamsInAllowed
		strtolower('a');  // disallowed: in except zone, param is forbidden by allowExceptParamsInAllowed
		strtolower('b');  // allowed: in except zone, param not forbidden
	}


	public function methodB(): void
	{
		crc32('a');       // allowed: not in except zone
		crc32('b');       // allowed: not in except zone
		strtolower('a');  // allowed: not in except zone
		strtolower('b');  // allowed: not in except zone
	}

}
