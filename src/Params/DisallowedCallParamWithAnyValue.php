<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

final class DisallowedCallParamWithAnyValue extends DisallowedCallParam
{

	public function matches(ConstantScalarType $type): bool
	{
		return true;
	}

}
