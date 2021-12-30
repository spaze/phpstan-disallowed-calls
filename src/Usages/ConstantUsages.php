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
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

/**
 * Reports on constant usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ConstFetch>
 */
class ConstantUsages implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var DisallowedConstant[] */
	private $disallowedConstants;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param DisallowedConstantFactory $disallowedConstantFactory
	 * @param array<array{constant?:string, message?:string, allowIn?:string[]}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, DisallowedConstantFactory $disallowedConstantFactory, array $disallowedConstants)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedConstants = $disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ConstFetch::class;
	}


	/**
	 * @param ConstFetch $node
	 * @param Scope $scope
	 * @return RuleError[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var ConstFetch $node */
		return $this->disallowedHelper->getDisallowedConstantMessage((string)$node->name, $scope, null, $this->disallowedConstants);
	}

}
