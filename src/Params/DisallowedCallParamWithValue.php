<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

class DisallowedCallParamWithValue implements DisallowedCallParam
{

	/** @var int|bool|string */
	private $value;


	/**
	 * @param int|bool|string $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}


	public function matches(ConstantScalarType $type): bool
	{
		return $this->value === $type->getValue();
	}


	/**
	 * @return int|bool|string
	 */
	public function getValue()
	{
		return $this->value;
	}

}
