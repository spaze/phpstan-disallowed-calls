<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

/**
 * @template T of int|bool|string|null
 */
abstract class DisallowedCallParamValue implements DisallowedCallParam
{

	/** @var ?int */
	private $position;

	/** @var ?string */
	private $name;

	/** @var T */
	private $value;


	abstract public function matches(ConstantScalarType $type): bool;


	/**
	 * @param int|null $position
	 * @param string|null $name
	 * @param T $value
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
	 * @return T
	 */
	public function getValue()
	{
		return $this->value;
	}

}
