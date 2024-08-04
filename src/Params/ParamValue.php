<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;

abstract class ParamValue implements Param
{

	abstract public function matches(Type $type): bool;


	final public function __construct(
		private readonly ?int $position,
		private readonly ?string $name,
		private readonly Type $type,
	) {
	}


	public function getPosition(): ?int
	{
		return $this->position;
	}


	public function getName(): ?string
	{
		return $this->name;
	}


	public function getType(): Type
	{
		return $this->type;
	}

}
