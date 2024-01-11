<?php
declare(strict_types = 1);

namespace Attributes;

#[Attribute]
class AttributeColumn
{

	public function __construct(
		?string $name = null,
		?string $type = null
	) {
	}

}
