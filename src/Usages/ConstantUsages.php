<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;

/**
 * Reports on constant usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ConstFetch>
 */
class ConstantUsages implements Rule
{

	/** @var list<DisallowedConstant> */
	private readonly array $disallowedConstants;


	/**
	 * @param array<array{constant?:string, message?:string, allowIn?:list<string>}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		private readonly DisallowedConstantRuleErrors $disallowedConstantRuleErrors,
		private readonly DisallowedConstantFactory $disallowedConstantFactory,
		array $disallowedConstants,
	) {
		$this->disallowedConstants = $this->disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ConstFetch::class;
	}


	/**
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var ConstFetch $node */
		return $this->disallowedConstantRuleErrors->get((string)$node->name, $scope, null, $this->disallowedConstants, ErrorIdentifiers::DISALLOWED_CONSTANT);
	}

}
