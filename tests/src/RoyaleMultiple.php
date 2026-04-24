<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class RoyaleMultiple
{

	public function methodA(): void
	{
		$foo = crc32('a');
	}


	public function methodB(): void
	{
		$foo = crc32('b');
	}


	public function methodC(): void
	{
		$foo = crc32('c');
	}

}
