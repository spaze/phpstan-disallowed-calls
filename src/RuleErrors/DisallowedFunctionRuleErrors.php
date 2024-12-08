<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class DisallowedFunctionRuleErrors
{

	private DisallowedCallsRuleErrors $disallowedCallsRuleErrors;

	private ReflectionProvider $reflectionProvider;

	private Normalizer $normalizer;

	private TypeResolver $typeResolver;


	public function __construct(
		DisallowedCallsRuleErrors $disallowedCallsRuleErrors,
		ReflectionProvider $reflectionProvider,
		Normalizer $normalizer,
		TypeResolver $typeResolver
	) {
		$this->disallowedCallsRuleErrors = $disallowedCallsRuleErrors;
		$this->reflectionProvider = $reflectionProvider;
		$this->normalizer = $normalizer;
		$this->typeResolver = $typeResolver;
	}


	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @param list<DisallowedCall> $disallowedCalls
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(FuncCall $node, Scope $scope, array $disallowedCalls): array
	{
		if ($node->name instanceof Name) {
			$namespacedName = $node->name->getAttribute('namespacedName');
			if ($namespacedName !== null && !($namespacedName instanceof Name)) {
				throw new ShouldNotHappenException();
			}
			$names = [$namespacedName, $node->name];
		} elseif ($node->name instanceof String_) {
			$names = [new Name($this->normalizer->normalizeNamespace($node->name->value))];
		} elseif ($node->name instanceof Variable) {
			$value = $this->typeResolver->getVariableStringValue($node->name, $scope);
			if (!is_string($value)) {
				return [];
			}
			$names = [new Name($this->normalizer->normalizeNamespace($value))];
		} else {
			return [];
		}
		$displayName = $node->name->getAttribute('originalName');
		if ($displayName !== null && !($displayName instanceof Name)) {
			throw new ShouldNotHappenException();
		}
		foreach ($names as $name) {
			if ($name && $this->reflectionProvider->hasFunction($name, $scope)) {
				$functionReflection = $this->reflectionProvider->getFunction($name, $scope);
				$definedIn = $functionReflection->isBuiltin() ? null : $functionReflection->getFileName();
			} else {
				$definedIn = null;
			}
			$message = $this->disallowedCallsRuleErrors->get($node, $scope, (string)$name, (string)($displayName ?? $name), $definedIn, $disallowedCalls, ErrorIdentifiers::DISALLOWED_FUNCTION);
			if ($message) {
				return $message;
			}
		}
		return [];
	}

}
