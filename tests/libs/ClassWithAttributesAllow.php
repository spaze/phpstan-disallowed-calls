<?php
declare(strict_types = 1);

namespace libs;

use Attributes\AttributeEntity;
use Waldo\Quux\Blade;

// disallowed, no $repositoryClass parameter specified
#[AttributeEntity]
class ClassWithAttributesAllow
{

	// disallowed, $repositoryClass present with any value
	#[AttributeEntity(repositoryClass: UserRepository::class, readOnly: false)]
	public function hasAvocado(): bool
	{
	}


	// allowed, $repositoryClass present with any value
	#[AttributeEntity(UserRepository::class)]
	public function hasTuna(): bool
	{
	}


	// allowed, $repositoryClass present with any value
	#[AttributeEntity(Blade::class)]
	public function hasKetchup(): bool
	{
	}

}
