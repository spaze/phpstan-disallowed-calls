<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

/**
 * Provides compatibility layer when running on PHPStan 1.x.
 * The whole file can be removed when PHPStan 2.x is required in `composer.json`.
 * This file is ignored in `phpstan.neon` in the `excludePaths.analyse` entry.
 */
class PHPStan1Compatibility
{

	/**
	 * @see https://github.com/phpstan/phpstan/blob/2.0.x/UPGRADING.md#minor-backward-compatibility-breaks-1:~:text=Rename%20Type%3A%3AisClassStringType()%20to%20Type%3A%3AisClassString()
	 */
	public static function isClassString(Type $type): TrinaryLogic
	{
		if (method_exists($type, 'isClassStringType')) {
			// PHPStan 1.x
			return $type->isClassStringType();
		}

		// PHPStan 2.x
		return $type->isClassString();
	}

}
