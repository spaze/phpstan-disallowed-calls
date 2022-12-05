<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

abstract class DisallowedCallParam
{

	/** @var ?int */
	private $position;

	/** @var ?string */
	private $name;

	/** @var int|bool|string|null */
	private $value;


	abstract public function matches(ConstantScalarType $type): bool;


	/**
	 * @param int|null $position
	 * @param string|null $name
	 * @param int|bool|string|null $value
	 */
	final public function __construct(?int $position, ?string $name, $value)
	{
		$this->position = $position;
		$this->name = $name;
		$this->value = $value;
	}


	public function getPosition(): ?int
	{
		return $this->position;
	}


	public function getName(): ?string
	{
		return $this->name;
	}


	/**
	 * @return bool|int|string|null
	 */
	public function getValue()
	{
		return $this->value;
	}

}
