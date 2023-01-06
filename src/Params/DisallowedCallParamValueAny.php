<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;

/**
 * @extends DisallowedCallParamValue<int|bool|string|null>
 */
final class DisallowedCallParamValueAny extends DisallowedCallParamValue
{

	public function matches(Type $type): bool
	{
		return true;
	}

}
