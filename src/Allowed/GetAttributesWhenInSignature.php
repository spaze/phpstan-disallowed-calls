<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ReflectionProvider;

class GetAttributesWhenInSignature
{

	private ReflectionProvider $reflectionProvider;

	/** @var class-string|null */
	private ?string $currentClass = null;

	private ?string $currentMethod = null;

	/** @var Name|null */
	private ?Name $currentFunction = null;


	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
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
	 * @return list<AttributeReflection>|null
	 */
	public function get(Scope $scope): ?array
	{
		if (
			$this->currentClass !== null
			&& $this->currentMethod !== null
			&& $scope->isInClass()
			&& $scope->getClassReflection()->getName() === $this->currentClass
		) {
			return $scope->getClassReflection()->getNativeMethod($this->currentMethod)->getAttributes();
		} elseif ($this->currentFunction !== null) {
			return $this->reflectionProvider->getFunction($this->currentFunction, $scope)->getAttributes();
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
	 * @param Name $functionName
	 * @return void
	 */
	public function setCurrentFunctionName(Name $functionName): void
	{
		$this->currentFunction = $functionName;
	}


	public function unsetCurrentFunctionName(): void
	{
		$this->currentFunction = null;
	}

}
