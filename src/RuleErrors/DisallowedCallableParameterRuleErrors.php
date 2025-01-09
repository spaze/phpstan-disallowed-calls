<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\ArgumentsNormalizer;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\TypeCombinator;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\PHPStan1Compatibility;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class DisallowedCallableParameterRuleErrors
{

	private TypeResolver $typeResolver;

	private DisallowedFunctionRuleErrors $disallowedFunctionRuleErrors;

	private DisallowedMethodRuleErrors $disallowedMethodRuleErrors;

	/** @var list<DisallowedCall> */
	private array $disallowedFunctionCalls;

	/** @var list<DisallowedCall> */
	private array $disallowedCalls;

	private ReflectionProvider $reflectionProvider;


	/**
	 * @param TypeResolver $typeResolver
	 * @param DisallowedFunctionRuleErrors $disallowedFunctionRuleErrors
	 * @param DisallowedMethodRuleErrors $disallowedMethodRuleErrors
	 * @param DisallowedCallFactory $disallowedCallFactory
	 * @param ReflectionProvider $reflectionProvider
	 * @param array $forbiddenFunctionCalls
	 * @phpstan-param ForbiddenCallsConfig $forbiddenFunctionCalls
	 * @param array $forbiddenMethodCalls
	 * @phpstan-param ForbiddenCallsConfig $forbiddenMethodCalls
	 * @param array $forbiddenStaticCalls
	 * @phpstan-param ForbiddenCallsConfig $forbiddenStaticCalls
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		TypeResolver $typeResolver,
		DisallowedFunctionRuleErrors $disallowedFunctionRuleErrors,
		DisallowedMethodRuleErrors $disallowedMethodRuleErrors,
		DisallowedCallFactory $disallowedCallFactory,
		ReflectionProvider $reflectionProvider,
		array $forbiddenFunctionCalls,
		array $forbiddenMethodCalls,
		array $forbiddenStaticCalls
	) {
		$this->typeResolver = $typeResolver;
		$this->disallowedFunctionRuleErrors = $disallowedFunctionRuleErrors;
		$this->disallowedMethodRuleErrors = $disallowedMethodRuleErrors;
		$this->disallowedFunctionCalls = $disallowedCallFactory->createFromConfig($forbiddenFunctionCalls);
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig(array_merge($forbiddenMethodCalls, $forbiddenStaticCalls));
		$this->reflectionProvider = $reflectionProvider;
	}


	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function getForFunction(FuncCall $node, Scope $scope): array
	{
		$ruleErrors = [];
		foreach ($this->typeResolver->getNamesFromCall($node, $scope) as $name) {
			if (!$this->reflectionProvider->hasFunction($name, $scope)) {
				continue;
			}
			$reflection = $this->reflectionProvider->getFunction($name, $scope);
			$errors = $this->getErrors($node, $scope, $reflection);
			if ($errors) {
				$ruleErrors = array_merge($ruleErrors, $errors);
			}
		}
		return $ruleErrors;
	}


	/**
	 * @param Name|Expr $class
	 * @param MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function getForMethod($class, CallLike $node, Scope $scope): array
	{
		$ruleErrors = [];
		$classType = $this->typeResolver->getType($class, $scope);
		if (PHPStan1Compatibility::isClassString($classType)->yes()) {
			$classType = $classType->getClassStringObjectType();
		}
		foreach ($classType->getObjectTypeOrClassStringObjectType()->getObjectClassNames() as $className) {
			if (!$this->reflectionProvider->hasClass($className)) {
				continue;
			}
			$classReflection = $this->reflectionProvider->getClass($className);
			foreach ($this->typeResolver->getNamesFromCall($node, $scope) as $name) {
				if (!$classReflection->hasMethod($name->toString())) {
					continue;
				}
				$reflection = $classReflection->getMethod($name->toString(), $scope);
				$errors = $this->getErrors($node, $scope, $reflection);
				if ($errors) {
					$ruleErrors = array_merge($ruleErrors, $errors);
				}
			}
		}
		return $ruleErrors;
	}


	/**
	 * @param Scope $scope
	 * @param FuncCall|MethodCall|StaticCall $node
	 * @param ExtendedMethodReflection|FunctionReflection $reflection
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	private function getErrors(CallLike $node, Scope $scope, $reflection): array
	{
		$ruleErrors = [];
		$parametersAcceptor = ParametersAcceptorSelector::selectFromArgs($scope, $node->getArgs(), $reflection->getVariants());
		$reorderedArgs = ArgumentsNormalizer::reorderArgs($parametersAcceptor, $node->getArgs()) ?? $node->getArgs();
		foreach ($parametersAcceptor->getParameters() as $key => $parameter) {
			if (!TypeCombinator::removeNull($parameter->getType())->isCallable()->yes() || !isset($reorderedArgs[$key])) {
				continue;
			}
			$callableType = $scope->getType($reorderedArgs[$key]->value);
			foreach ($callableType->getConstantStrings() as $constantString) {
				$errors = $this->disallowedFunctionRuleErrors->getByString($constantString->getValue(), $scope, $this->disallowedFunctionCalls);
				if ($errors) {
					$ruleErrors = array_merge($ruleErrors, $errors);
				}
			}
			foreach ($callableType->getConstantArrays() as $constantArray) {
				foreach ($constantArray->findTypeAndMethodNames() as $typeAndMethodName) {
					if ($typeAndMethodName->isUnknown()) {
						continue;
					}
					$method = $typeAndMethodName->getMethod();
					foreach ($typeAndMethodName->getType()->getObjectClassNames() as $class) {
						$errors = $this->disallowedMethodRuleErrors->getByString($class, $method, $scope, $this->disallowedCalls);
						if ($errors) {
							$ruleErrors = array_merge($ruleErrors, $errors);
						}
					}
				}
			}
		}
		return $ruleErrors;
	}

}
