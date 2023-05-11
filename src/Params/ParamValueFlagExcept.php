<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;

/**
 * @extends ParamValue<int>
 */
class ParamValueFlagExcept extends ParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 */
	public function matches(Type $type): bool
	{
		if (!$type instanceof ConstantIntegerType) {
			throw new UnsupportedParamTypeException();
		}
		return ($this->getValue() & $type->getValue()) === 0;
	}

}
