<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

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
	 * @param array<array{constant?:string, message?:string, allowIn?:string[]}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $disallowedConstants)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedConstants = $this->disallowedHelper->createConstantsFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ConstFetch::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var ConstFetch $node */
		return $this->disallowedHelper->getDisallowedConstantMessage((string)$node->name, $scope, null, $this->disallowedConstants);
	}

}
