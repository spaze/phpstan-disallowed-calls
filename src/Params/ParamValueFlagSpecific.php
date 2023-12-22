<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Params;

use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;

class ParamValueFlagSpecific extends ParamValueFlag
{

	/**
	 * @throws UnsupportedParamTypeException
	 * @throws UnsupportedParamTypeInConfigException
	 */
	public function matches(Type $type): bool
	{
		return $this->isFlagSet($type);
	}

}
