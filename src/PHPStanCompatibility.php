<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\PropertyHook;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
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


	/**
	 * @return list<string>|null null if the method is not a property hook
	 */
	public static function getPropertyHookAttributes(MethodReflection $method, ClassReflection $class): ?array
	{
		if (!method_exists($method, 'isPropertyHook')) {
			// PHPStan < 2.1.0, which doesn't analyse property hooks
			return null;
		} else {
			// PHPStan 2.1.0+
			if (!$method->isPropertyHook()) {
				return null;
			}
			$hook = $class->getNativeReflection()->getProperty($method->getHookedPropertyName())->getHooks()[$method->getPropertyHookName()] ?? null;
			return $hook !== null ? array_map(static fn($a) => $a->getName(), $hook->getAttributes()) : [];
		}
	}


	/**
	 * Always null with PHPStan 1.x, its php-parser doesn't know property hooks.
	 *
	 * @return list<string>|null null if the node is not a property hook
	 */
	public static function getPropertyHookNodeAttributes(?Node $node): ?array
	{
		if (!$node instanceof PropertyHook) {
			return null;
		}
		$names = [];
		foreach ($node->attrGroups as $attrGroup) {
			foreach ($attrGroup->attrs as $attr) {
				$names[] = $attr->name->toString();
			}
		}
		return $names;
	}


	/**
	 * @return list<string>
	 */
	public static function getAllPropertyHookAttributes(ClassReflection $class): array
	{
		if (!class_exists(PropertyHook::class)) {
			// PHPStan 1.x, its php-parser doesn't know property hooks, and PHP 8.4's own getHooks() can't be called on its reflection adapter
			return [];
		}
		$names = [];
		foreach ($class->getNativeReflection()->getProperties() as $property) {
			foreach ($property->getHooks() as $hook) {
				foreach ($hook->getAttributes() as $attribute) {
					$names[] = $attribute->getName();
				}
			}
		}
		return $names;
	}

}
