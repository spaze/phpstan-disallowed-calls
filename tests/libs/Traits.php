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


trait YetAnotherTrait
{

	public static function zzTop(): void
	{
	}

}


final class TestClass
{
	use TestTrait;
	use AnotherTrait;
	use YetAnotherTrait;
}


final class AnotherTestClass
{
	use TestTrait;
	use AnotherTrait;
	use YetAnotherTrait;
}
