<?php
declare(strict_types = 1);

namespace Fiction\Pulp;

class Royale
{

	public static function leBigMac(): void
	{
	}


	public static function withCheese(): void
	{
	}


	public static function WithBadCheese(): void
	{
		$foo = md5_file(__FILE__);
	}


	public static function withoutCheese(int $patty, int $bun, int $tomato): void
	{
		$foo = sha1_file(__FILE__, true);
	}

}
