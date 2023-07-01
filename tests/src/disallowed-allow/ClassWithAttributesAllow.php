<?php
declare(strict_types = 1);

namespace Attributes;

use Waldo\Quux\Blade;

#[\Attributes\AttributeEntity] // disallowed, no $repositoryClass parameter specified
class ClassWithAttributesAllow
{

	#[\Attributes\AttributeEntity(repositoryClass: \Attributes\UserRepository::class, readOnly: false)] // disallowed, $repositoryClass present with any value
	public function hasAvocado(): bool
	{
	}


	#[\Attributes\AttributeEntity(\Attributes\UserRepository::class)] // allowed, $repositoryClass present with any value
	public function hasTuna(): bool
	{
	}


	#[\Attributes\AttributeEntity(Blade::class)] // allowed, $repositoryClass present with any value
	public function hasKetchup(): bool
	{
	}

}
