<?php
declare(strict_types = 1);

namespace Inheritance;

abstract class Base
{

	public const ALL = 'your';

	public const BELONG = 'us';


	public function x(): void
	{
	}


	public static function woofer(): void
	{
	}

}


final class Sub extends Base
{
}
