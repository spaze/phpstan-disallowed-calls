<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

class DisallowedCallParamExceptValue extends DisallowedCallParam
{

	public function matches(ConstantScalarType $type): bool
	{
		return $this->getValue() !== $type->getValue();
	}

}
