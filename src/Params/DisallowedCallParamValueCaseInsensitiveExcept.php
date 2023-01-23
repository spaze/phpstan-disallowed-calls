<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
class DisallowedCallParamValueCaseInsensitiveExcept extends DisallowedCallParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 */
	public function matches(Type $type): bool
	{
		if (!$type instanceof ConstantScalarType) {
			throw new UnsupportedParamTypeException();
		}
		$a = is_string($this->getValue()) ? strtolower($this->getValue()) : $this->getValue();
		$b = $type instanceof ConstantStringType ? strtolower($type->getValue()) : $type->getValue();
		return $a !== $b;
	}

}
