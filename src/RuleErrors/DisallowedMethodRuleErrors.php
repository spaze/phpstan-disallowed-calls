<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class DisallowedMethodRuleErrors
{

	/** @var DisallowedCallsRuleErrors */
	private $disallowedCallsRuleErrors;

	/** @var TypeResolver */
	private $typeResolver;

	/** @var Formatter */
	private $formatter;


	public function __construct(
		DisallowedCallsRuleErrors $disallowedCallsRuleErrors,
		TypeResolver $typeResolver,
		Formatter $formatter
	) {
		$this->disallowedCallsRuleErrors = $disallowedCallsRuleErrors;
		$this->typeResolver = $typeResolver;
		$this->formatter = $formatter;
	}


	/**
	 * @param Name|Expr $class
	 * @param MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @param list<DisallowedCall> $disallowedCalls
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get($class, CallLike $node, Scope $scope, array $disallowedCalls): array
	{
		if (!isset($node->name) || !($node->name instanceof Identifier)) {
			return [];
		}

		$calledOnType = $this->typeResolver->getType($class, $scope);
		if ($calledOnType->canCallMethods()->yes() && $calledOnType->hasMethod($node->name->name)->yes()) {
			$method = $calledOnType->getMethod($node->name->name, $scope);
			$declaringClass = $method->getDeclaringClass();
			$classNames = $calledOnType->getObjectClassNames();
			if (count($classNames) === 0) {
				$calledAs = null;
			} else {
				$calledAs = $this->formatter->getFullyQualified($this->formatter->formatIdentifier($classNames), $method);
			}

			$ruleErrors = $this->getRuleErrors(array_values($declaringClass->getTraits()), $method, $node, $scope, $calledAs, $disallowedCalls);
			if ($ruleErrors) {
				return $ruleErrors;
			}
			$ruleErrors = $this->getRuleErrors(array_values($declaringClass->getInterfaces()), $method, $node, $scope, $calledAs, $disallowedCalls);
			if ($ruleErrors) {
				return $ruleErrors;
			}
		} else {
			return [];
		}
		return $this->getRuleErrors([$declaringClass], $method, $node, $scope, $calledAs, $disallowedCalls);
	}


	/**
	 * @param list<ClassReflection> $classes
	 * @param list<DisallowedCall> $disallowedCalls
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	private function getRuleErrors(array $classes, MethodReflection $method, CallLike $node, Scope $scope, ?string $calledAs, array $disallowedCalls): array
	{
		foreach ($classes as $class) {
			if ($class->hasMethod($method->getName())) {
				$declaredAs = $this->formatter->getFullyQualified($class->getDisplayName(false), $method);
				$ruleErrors = $this->disallowedCallsRuleErrors->get($node, $scope, $declaredAs, $calledAs, $class->getFileName(), $disallowedCalls);
				if ($ruleErrors) {
					return $ruleErrors;
				}
			}
		}
		return [];
	}

}
