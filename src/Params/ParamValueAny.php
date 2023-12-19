<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;

final class ParamValueAny extends ParamValue
{

	public function matches(Type $type): bool
	{
		return true;
	}

}
