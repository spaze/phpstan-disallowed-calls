<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;

/**
 * @extends ParamValue<int|bool|string|null>
 */
class ParamValueSpecific extends ParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 */
	public function matches(Type $type): bool
	{
		if (!$type->isConstantScalarValue()->yes()) {
			throw new UnsupportedParamTypeException();
		}
		return $type->getConstantScalarValues() === [$this->getValue()];
	}

}
