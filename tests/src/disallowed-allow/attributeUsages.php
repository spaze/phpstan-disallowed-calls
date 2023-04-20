<?php
declare(strict_types = 1);

namespace Sushi;

use Foo\Bar\AttributeEntity;

#[AttributeEntity]
class CaliforniaRoll
{

	#[AttributeEntity(repositoryClass: UserRepository::class, readOnly: false)]
	public function hasAvocado(): bool
	{
	}


	#[AttributeEntity(UserRepository::class)]
	public function hasTuna(): bool
	{
	}

}
