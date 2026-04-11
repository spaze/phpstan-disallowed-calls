<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

enum UsagePosition
{

	case ParamType;
	case ReturnType;

}
