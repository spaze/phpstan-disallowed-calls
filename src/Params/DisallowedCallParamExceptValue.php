<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

class DisallowedCallParamExceptValue implements DisallowedCallParam
{

	/** @var integer|boolean|string */
	private $value;


	/**
	 * @param integer|boolean|string $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}


	public function matches(ConstantScalarType $type): bool
	{
		return $this->value !== $type->getValue();
	}


	/**
	 * @return integer|boolean|string
	 */
	public function getValue()
	{
		return $this->value;
	}

}
