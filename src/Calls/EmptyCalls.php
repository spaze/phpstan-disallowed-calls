<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\Empty_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on dynamically calling empty().
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Empty_>
 */
class EmptyCalls implements Rule
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
		array $forbiddenCalls,
	) {
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return Empty_::class;
	}


	/**
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedCallsRuleErrors->get(null, $scope, 'empty', 'empty', null, $this->disallowedCalls, ErrorIdentifiers::DISALLOWED_EMPTY);
	}

}
