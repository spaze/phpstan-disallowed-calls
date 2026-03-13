<?php
declare(strict_types = 1);

use Traits\YetAnotherTrait;

// regular instance properties allowed by path
$royale = new Fiction\Pulp\Royale();
echo $royale->whopper;

echo $royale->{'whopper'};

$prop = 'whopper';
echo $royale->{$prop};

(new DateInterval('P4D'))->days;

// parent class property allowed by path
echo (new Inheritance\Sub())->property;

// trait property allowed by path
echo (new Traits\TestClass())->publicTraitProperty;

class ClassWithProperties
{
	use YetAnotherTrait;

	private int $privateProperty = 303;

	public static int $publicStaticProperty = 808;

	private static int $privateStaticProperty = 909;

	public function method(): void
	{
		// trait properties, private and protected properties allowed by path
		echo $this->publicTraitProperty;
		echo $this->protectedTraitProperty;
		echo $this->privateProperty;
		echo self::$publicStaticTraitProperty;
		echo self::$publicStaticProperty;
		echo self::$privateStaticProperty;
	}


	public function okHere(): void
	{
		// allowed by path
		(new DateInterval('PT5M'))->d;
		echo ClassWithProperties::$publicStaticProperty;
	}

}

// regular static property allowed by path
echo ClassWithProperties::$publicStaticProperty;

// regular static trait property allowed by path
echo ClassWithProperties::$publicStaticTraitProperty;
