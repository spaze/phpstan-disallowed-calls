<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\Exit_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on dynamically calling exit() & die().
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Exit_>
 */
class ExitDieCalls implements Rule
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
		return Exit_::class;
	}


	/**
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$kind = $node->getAttribute('kind', Exit_::KIND_DIE) === Exit_::KIND_EXIT ? 'exit' : 'die';
		return $this->disallowedCallsRuleErrors->get(null, $scope, $kind, $kind, null, $this->disallowedCalls, $kind === 'exit' ? ErrorIdentifiers::DISALLOWED_EXIT : ErrorIdentifiers::DISALLOWED_DIE);
	}

}
