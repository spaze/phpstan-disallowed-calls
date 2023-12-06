<?php
declare(strict_types = 1);

namespace Attributes;

use Waldo\Quux\Blade;

#[\Attributes\AttributeEntity] // allowed by path in AttributeUsagesTest, disallowed in AttributeUsagesAllowParamsMultipleTest because no $repositoryClass parameter specified
class ClassWithAttributesAllow
{

	#[AttributeEntity] // allowed by path in all tests
	private const MAYO = true;

	#[AttributeEntity] // allowed by path in all tests
	public $cheddar = 'plz';

	#[AttributeEntity] // disallowed
	public static $pepper = 'ofc';


	#[\Attributes\AttributeEntity(repositoryClass: \Attributes\UserRepository::class, readOnly: false)] // allowed by path in AttributeUsagesTest, disallowed in AttributeUsagesAllowParamsMultipleTest because $repositoryClass has other value
	public function hasAvocado(): bool
	{
	}


	#[\Attributes\AttributeEntity(\Attributes\UserRepository::class)] // allowed by path in AttributeUsagesTest, disallowed in AttributeUsagesAllowParamsMultipleTest because $repositoryClass has other value
	public function hasTuna(): bool
	{
	}


	#[\Attributes\AttributeEntity(Blade::class)] // allowed in all tests, $repositoryClass present with allowed value
	public function hasKetchup(): bool
	{
	}


	#[AttributeClass()] // allowed by path in all tests
	public function hasPineapple(
		#[AttributeEntity] // allowed by path in all tests
		bool $really
	): bool {
	}

}
