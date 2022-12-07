<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\ConstantScalarType;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
final class DisallowedCallParamValueValueAny extends DisallowedCallParamValue
{

	public function matches(ConstantScalarType $type): bool
	{
		return true;
	}

}
