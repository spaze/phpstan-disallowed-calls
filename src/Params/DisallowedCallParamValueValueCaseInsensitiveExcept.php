<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantScalarType;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
class DisallowedCallParamValueValueCaseInsensitiveExcept extends DisallowedCallParamValue
{

	public function matches(ConstantScalarType $type): bool
	{
		$a = is_string($this->getValue()) ? strtolower($this->getValue()) : $this->getValue();
		$b = $type instanceof ConstantStringType ? strtolower($type->getValue()) : $type->getValue();
		return $a !== $b;
	}

}
