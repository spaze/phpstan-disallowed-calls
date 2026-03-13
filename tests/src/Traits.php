<?php
declare(strict_types = 1);

namespace Traits;


trait AnotherTrait
{

	public static function zz(): void
	{
	}

}


trait YetAnotherTrait
{

	protected int $protectedTraitProperty = 1;

	public int $publicTraitProperty = 2;

	public static int $publicStaticTraitProperty = 3;

	public function zzTop(): void
	{
		echo $this->protectedTraitProperty;
		echo $this->publicTraitProperty;
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
