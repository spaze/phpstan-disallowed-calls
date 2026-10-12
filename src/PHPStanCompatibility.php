<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

/**
 * Provides compatibility layer when running on older PHPStan versions.
 * The whole file can be removed when PHPStan 2.1.26+ is required in `composer.json`.
 * This file is ignored in `phpstan.neon` in the `excludePaths.analyse` entry.
 */
class PHPStanCompatibility
{

	/**
	 * @see https://github.com/phpstan/phpstan/blob/2.0.x/UPGRADING.md#minor-backward-compatibility-breaks-1:~:text=Rename%20Type%3A%3AisClassStringType()%20to%20Type%3A%3AisClassString()
	 */
	public static function isClassString(Type $type): TrinaryLogic
	{
		if (method_exists($type, 'isClassStringType')) {
			// PHPStan 1.x
			return $type->isClassStringType();
		} else {
			// PHPStan 2.x
			return $type->isClassString();
		}
	}


	public static function hasInstanceProperty(string $property, ClassReflection $class): bool
	{
		if (!method_exists($class, 'hasInstanceProperty')) {
			// PHPStan < 2.1.26
			return $class->hasProperty($property);
		} else {
			// PHPStan 2.1.26+
			return $class->hasInstanceProperty($property);
		}
	}


	public static function getInstanceProperty(string $property, ClassReflection $class, Scope $scope): PropertyReflection
	{
		if (!method_exists($class, 'getInstanceProperty')) {
			// PHPStan < 2.1.26
			return $class->getProperty($property, $scope);
		} else {
			// PHPStan 2.1.26+
			return $class->getInstanceProperty($property, $scope);
		}
	}


	public static function hasStaticProperty(string $property, ClassReflection $class): bool
	{
		if (!method_exists($class, 'hasStaticProperty')) {
			// PHPStan < 2.1.26
			return $class->hasProperty($property);
		} else {
			// PHPStan 2.1.26+
			return $class->hasStaticProperty($property);
		}
	}


	public static function getStaticProperty(string $property, ClassReflection $class, Scope $scope): PropertyReflection
	{
		if (!method_exists($class, 'getStaticProperty')) {
			// PHPStan < 2.1.26
			return $class->getProperty($property, $scope);
		} else {
			// PHPStan 2.1.26+
			return $class->getStaticProperty($property);
		}
	}

}
