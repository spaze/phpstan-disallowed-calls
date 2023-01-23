<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
class DisallowedCallParamValueExcept extends DisallowedCallParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 */
	public function matches(Type $type): bool
	{
		if (!$type instanceof ConstantScalarType) {
			throw new UnsupportedParamTypeException();
		}
		return $this->getValue() !== $type->getValue();
	}

}
