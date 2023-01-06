<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\Type;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
class DisallowedCallParamValueSpecific extends DisallowedCallParamValue
{

	public function matches(Type $type): bool
	{
		if (!$type instanceof ConstantScalarType) {
			return false;
		}
		return $this->getValue() === $type->getValue();
	}

}
