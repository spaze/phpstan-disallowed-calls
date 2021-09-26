<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantScalarType;

class DisallowedCallParamExceptCaseInsensitiveValue implements DisallowedCallParam
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
		$a = is_string($this->value) ? strtolower($this->value) : $this->value;
		$b = $type instanceof ConstantStringType ? strtolower($type->getValue()) : $type->getValue();
		return $a !== $b;
	}


	/**
	 * @return int|bool|string
	 */
	public function getValue()
	{
		return $this->value;
	}

}
