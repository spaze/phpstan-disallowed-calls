<?php
declare(strict_types = 1);

namespace Attributes;

use Waldo\Quux\Blade;

#[AttributeEntity]
class ClassWithAttributes
{

	#[AttributeEntity]
	private const MAYO = true;

	#[AttributeEntity]
	public $cheddar = 'plz';

	#[AttributeEntity]
	public static $pepper = 'ofc';

	#[AttributeColumn(name: 'start_date_parsed', type: 'datetime')]
	private $fries = 'large';


	#[AttributeEntity(repositoryClass: UserRepository::class, readOnly: false)]
	public function hasAvocado(): bool
	{
	}


	#[AttributeEntity(UserRepository::class)]
	public function hasTuna(): bool
	{
	}


	#[AttributeEntity(Blade::class)]
	public function hasKetchup(): bool
	{
	}


	#[AttributeClass()]
	public function hasPineapple(
		#[AttributeEntity]
		bool $really
	): bool {
	}

}
