<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;

/**
 * @extends ParamValue<int|bool|string|null>
 */
class ParamValueCaseInsensitiveExcept extends ParamValue
{

	/**
	 * @throws UnsupportedParamTypeException
	 */
	public function matches(Type $type): bool
	{
		if (!$type->isConstantScalarValue()->yes()) {
			throw new UnsupportedParamTypeException();
		}
		$values = [];
		foreach ($type->getConstantScalarValues() as $value) {
			$values[] = $this->getLowercaseValue($value);
		}
		return !in_array($this->getLowercaseValue($this->getValue()), $values, true);
	}


	/**
	 * @param mixed $value
	 * @return mixed
	 */
	private function getLowercaseValue($value)
	{
		return is_string($value) ? strtolower($value) : $value;
	}

}
