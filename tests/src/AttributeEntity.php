<?php
declare(strict_types = 1);

namespace Attributes;

use Attribute;

#[Attribute]
class AttributeEntity
{

	public function __construct(?string $repositoryClass = null, bool $readOnly = false)
	{
	}

}
