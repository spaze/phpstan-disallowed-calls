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

/**
 * Reports on constant usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ConstFetch>
 */
class ConstantUsages implements Rule
{

	/** @var DisallowedConstantRuleErrors */
	private $disallowedConstantRuleError;

	/** @var list<DisallowedConstant> */
	private $disallowedConstants;


	/**
	 * @param DisallowedConstantRuleErrors $disallowedConstantRuleErrors
	 * @param DisallowedConstantFactory $disallowedConstantFactory
	 * @param array<array{constant?:string, message?:string, allowIn?:list<string>}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedConstantRuleErrors $disallowedConstantRuleErrors, DisallowedConstantFactory $disallowedConstantFactory, array $disallowedConstants)
	{
		$this->disallowedConstantRuleError = $disallowedConstantRuleErrors;
		$this->disallowedConstants = $disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ConstFetch::class;
	}


	/**
	 * @param ConstFetch $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var ConstFetch $node */
		return $this->disallowedConstantRuleError->get((string)$node->name, $scope, null, $this->disallowedConstants);
	}

}
