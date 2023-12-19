<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;

abstract class ParamValueFlag extends ParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 * @throws UnsupportedParamTypeInConfigException
	 */
	protected function isFlagSet(Type $type): bool
	{
		if (!$type instanceof ConstantIntegerType) {
			throw new UnsupportedParamTypeException();
		}
		foreach ($this->getType()->getConstantScalarValues() as $value) {
			if (!is_int($value)) {
				throw new UnsupportedParamTypeInConfigException($this->getPosition(), $this->getName(), gettype($value) . ' of ' . $this->getType()->describe(VerbosityLevel::precise()));
			}
			if (($value & $type->getValue()) !== 0) {
				return true;
			}
		}
		return false;
	}

}
