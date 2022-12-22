<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\ConstantScalarType;

/**
 * @extends DisallowedCallParamValue<int>
 */
class DisallowedCallParamValueFlagSpecific extends DisallowedCallParamValue
{

	public function matches(ConstantScalarType $type): bool
	{
		if (!$type instanceof ConstantIntegerType) {
			return false;
		}
		return ($this->getValue() & $type->getValue()) !== 0;
	}

}
