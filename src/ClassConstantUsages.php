<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\TypeWithClassName;

/**
 * Reports on class constant usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ClassConstFetch>
 */
class ClassConstantUsages implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var DisallowedConstant[] */
	private $disallowedConstants;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param array<array{function?:string, method?:string, message?:string, allowIn?:string[]}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $disallowedConstants)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedConstants = $this->disallowedHelper->createConstantsFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ClassConstFetch::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node instanceof ClassConstFetch)) {
			throw new ShouldNotHappenException(sprintf('$node should be %s but is %s', ClassConstFetch::class, get_class($node)));
		}
		if (!($node->name instanceof Identifier)) {
			throw new ShouldNotHappenException(sprintf('$node->name should be %s but is %s', Identifier::class, get_class($node->name)));
		}
		$constant = (string)$node->name;
		$usedOnType = $this->disallowedHelper->resolveType($node->class, $scope);

		if (strtolower($constant) === 'class') {
			return [];
		}

		$displayName = ($usedOnType instanceof TypeWithClassName ? $this->getFullyQualified($usedOnType->getClassName(), $constant) : null);
		$className = ($usedOnType instanceof ConstantStringType
			? ltrim($usedOnType->getValue(), '\\')
			: $usedOnType->getConstant($constant)->getDeclaringClass()->getDisplayName()
		);
		$constant = $this->getFullyQualified($className, $constant);

		return $this->disallowedHelper->getDisallowedConstantMessage($constant, $scope, $displayName, $this->disallowedConstants);
	}


	private function getFullyQualified(string $class, string $constant): string
	{
		return "{$class}::{$constant}";
	}

}
