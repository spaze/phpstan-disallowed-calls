<?php
declare(strict_types = 1);

use Traits\YetAnotherTrait;

// disallowed regular instance properties
$royale = new Fiction\Pulp\Royale();
echo $royale->whopper;

echo $royale->{'whopper'};

$prop = 'whopper';
echo $royale->{$prop};

(new DateInterval('P4D'))->d;

// disallowed parent class property
echo (new Inheritance\Sub())->property;

// disallowed trait property
echo (new Traits\TestClass())->publicTraitProperty;

class ClassWithProperties
{
	use YetAnotherTrait;

	private int $privateProperty = 303;

	public static int $publicStaticProperty = 808;

	private static int $privateStaticProperty = 909;

	public function method(): void
	{
		// disallowed trait properties, private and protected properties
		echo $this->publicTraitProperty;
		echo $this->protectedTraitProperty;
		echo $this->privateProperty;
		echo self::$publicStaticTraitProperty;
		echo self::$publicStaticProperty;
		echo self::$privateStaticProperty;
	}


	public function okHere(): void
	{
		// allowed by allowInMethods
		(new DateInterval('PT5M'))->d;
		echo ClassWithProperties::$publicStaticProperty;
	}

}

// disallowed regular static property
echo ClassWithProperties::$publicStaticProperty;

// disallowed regular static trait property
echo ClassWithProperties::$publicStaticTraitProperty;
