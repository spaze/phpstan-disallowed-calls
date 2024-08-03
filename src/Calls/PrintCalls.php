<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\Print_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on dynamically calling print().
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Print_>
 */
class PrintCalls implements Rule
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
		readonly DisallowedCallFactory $disallowedCallFactory,
		readonly array $forbiddenCalls,
	) {
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return Print_::class;
	}


	/**
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedCallsRuleErrors->get(null, $scope, 'print', 'print', null, $this->disallowedCalls, ErrorIdentifiers::DISALLOWED_PRINT);
	}

}
