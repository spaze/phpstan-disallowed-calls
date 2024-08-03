<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
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

	/** @var list<DisallowedCall> */
	private readonly array $disallowedCalls;


	/**
	 * @phpstan-param ForbiddenCallsConfig $forbiddenCalls
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		private readonly DisallowedCallsRuleErrors $disallowedCallsRuleErrors,
		DisallowedCallFactory $disallowedCallFactory,
		private readonly ReflectionProvider $reflectionProvider,
		array $forbiddenCalls,
	) {
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return FuncCall::class;
	}


	/**
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node->name instanceof Name)) {
			return [];
		}
		$namespacedName = $node->name->getAttribute('namespacedName');
		if ($namespacedName !== null && !($namespacedName instanceof Name)) {
			throw new ShouldNotHappenException();
		}
		$displayName = $node->name->getAttribute('originalName');
		if ($displayName !== null && !($displayName instanceof Name)) {
			throw new ShouldNotHappenException();
		}
		foreach ([$namespacedName, $node->name] as $name) {
			if ($name && $this->reflectionProvider->hasFunction($name, $scope)) {
				$functionReflection = $this->reflectionProvider->getFunction($name, $scope);
				$definedIn = $functionReflection->isBuiltin() ? null : $functionReflection->getFileName();
			} else {
				$definedIn = null;
			}
			$message = $this->disallowedCallsRuleErrors->get($node, $scope, (string)$name, (string)($displayName ?? $node->name), $definedIn, $this->disallowedCalls, ErrorIdentifiers::DISALLOWED_FUNCTION);
			if ($message) {
				return $message;
			}
		}
		return [];
	}

}
