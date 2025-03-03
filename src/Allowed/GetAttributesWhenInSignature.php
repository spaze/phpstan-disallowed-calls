<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\FakeReflectionAttribute;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionAttribute;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute as BetterReflectionAttribute;
use PHPStan\BetterReflection\Reflector\Reflector;

class GetAttributesWhenInSignature
{

	private Reflector $reflector;

	/** @var class-string|null */
	private ?string $currentClass = null;

	private ?string $currentMethod = null;

	/** @var string|null */
	private ?string $currentFunction = null;


	public function __construct(Reflector $reflector)
	{
		$this->reflector = $reflector;
	}


	/**
	 * Emulates the missing $scope->getMethodOrFunctionSignature().
	 *
	 * Because $scope->getFunction() returns null when the node, like for example a namespace node (instance of FullyQualified),
	 * is inside the method or the function signature, it's impossible to get to the current method or function reflection using $scope to get its attributes.
	 * The hacky solution is to store the current method name in a ClassMethod rule, read it here, and unset it in a InClassMethodNode rule,
	 * or the function name in a Function_ and a InFunctionNode rules.
	 *
	 * @param Scope $scope
	 * @return list<FakeReflectionAttribute|ReflectionAttribute|BetterReflectionAttribute>|null
	 */
	public function get(Scope $scope): ?array
	{
		if (
			$this->currentClass !== null
			&& $this->currentMethod !== null
			&& $scope->isInClass()
			&& $scope->getClassReflection()->getName() === $this->currentClass
		) {
			return $scope->getClassReflection()->getNativeReflection()->getMethod($this->currentMethod)->getAttributes();
		} elseif ($this->currentFunction !== null) {
			return $this->reflector->reflectFunction($this->currentFunction)->getAttributes();
		}
		return null;
	}


	/**
	 * @param class-string $className
	 * @param string $methodName
	 * @return void
	 */
	public function setCurrentClassMethodName(string $className, string $methodName): void
	{
		$this->currentClass = $className;
		$this->currentMethod = $methodName;
	}


	public function unsetCurrentClassMethodName(): void
	{
		$this->currentClass = $this->currentMethod = null;
	}


	/**
	 * @param string $functionName
	 * @return void
	 */
	public function setCurrentFunctionName(string $functionName): void
	{
		$this->currentFunction = $functionName;
	}


	public function unsetCurrentFunctionName(): void
	{
		$this->currentFunction = null;
	}

}
