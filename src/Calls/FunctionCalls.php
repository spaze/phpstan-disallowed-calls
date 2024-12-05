<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on dynamically calling a disallowed function.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<FuncCall>
 */
class FunctionCalls implements Rule
{

	private DisallowedCallsRuleErrors $disallowedCallsRuleErrors;

	/** @var list<DisallowedCall> */
	private array $disallowedCalls;

	private ReflectionProvider $reflectionProvider;

	private Normalizer $normalizer;


	/**
	 * @param DisallowedCallsRuleErrors $disallowedCallsRuleErrors
	 * @param DisallowedCallFactory $disallowedCallFactory
	 * @param ReflectionProvider $reflectionProvider
	 * @param Normalizer $normalizer
	 * @param array $forbiddenCalls
	 * @phpstan-param ForbiddenCallsConfig $forbiddenCalls
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		DisallowedCallsRuleErrors $disallowedCallsRuleErrors,
		DisallowedCallFactory $disallowedCallFactory,
		ReflectionProvider $reflectionProvider,
		Normalizer $normalizer,
		array $forbiddenCalls
	) {
		$this->disallowedCallsRuleErrors = $disallowedCallsRuleErrors;
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
		$this->reflectionProvider = $reflectionProvider;
		$this->normalizer = $normalizer;
	}


	public function getNodeType(): string
	{
		return FuncCall::class;
	}


	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->name instanceof Name) {
			$namespacedName = $node->name->getAttribute('namespacedName');
			if ($namespacedName !== null && !($namespacedName instanceof Name)) {
				throw new ShouldNotHappenException();
			}
			$names = [$namespacedName, $node->name];
		} elseif ($node->name instanceof String_) {
			$names = [new Name($this->normalizer->normalizeNamespace($node->name->value))];
		} elseif ($node->name instanceof Node\Expr\Variable && is_string($node->name->name)) {
			$value = $scope->getVariableType($node->name->name)->getConstantScalarValues()[0];
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
			$message = $this->disallowedCallsRuleErrors->get($node, $scope, (string)$name, (string)($displayName ?? $name), $definedIn, $this->disallowedCalls, ErrorIdentifiers::DISALLOWED_FUNCTION);
			if ($message) {
				return $message;
			}
		}
		return [];
	}

}
