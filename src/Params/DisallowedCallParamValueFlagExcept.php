<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Type;

/**
 * @extends DisallowedCallParamValue<int>
 */
class DisallowedCallParamValueFlagExcept extends DisallowedCallParamValue
{

	public function matches(Type $type): bool
	{
		if (!$type instanceof ConstantIntegerType) {
			return false;
		}
		return ($this->getValue() & $type->getValue()) === 0;
	}

}
