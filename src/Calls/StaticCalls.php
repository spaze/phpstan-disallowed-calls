<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;

/**
 * Reports on statically calling a disallowed method or two.
 *
 * Dynamic calls have a different rule, <code>MethodCalls</code>
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<StaticCall>
 */
class StaticCalls implements Rule
{

	/** @var list<DisallowedCall> */
	private readonly array $disallowedCalls;


	/**
	 * @phpstan-param ForbiddenCallsConfig $forbiddenCalls
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		private readonly DisallowedMethodRuleErrors $disallowedMethodRuleErrors,
		DisallowedCallFactory $disallowedCallFactory,
		array $forbiddenCalls,
	) {
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return StaticCall::class;
	}


	/**
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedMethodRuleErrors->get($node->class, $node, $scope, $this->disallowedCalls);
	}

}
