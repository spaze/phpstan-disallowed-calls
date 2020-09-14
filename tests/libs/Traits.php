<?php
declare(strict_types = 1);

namespace Traits;

trait TestTrait
{

	public function x(): void
	{
	}


	public function y(): void
	{
	}


	public static function z(): void
	{
	}

}


trait AnotherTrait
{

	public static function zz(): void
	{
	}

}


final class TestClass
{
	use TestTrait;
	use AnotherTrait;
}


final class AnotherTestClass
{
	use TestTrait;
	use AnotherTrait;
}
