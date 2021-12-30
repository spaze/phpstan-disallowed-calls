<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\VerbosityLevel;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

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
	 * @param DisallowedConstantFactory $disallowedConstantFactory
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[]}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, DisallowedConstantFactory $disallowedConstantFactory, array $disallowedConstants)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedConstants = $disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ClassConstFetch::class;
	}


	/**
	 * @param ClassConstFetch $node
	 * @param Scope $scope
	 * @return RuleError[]
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
		if ($usedOnType instanceof ConstantStringType) {
			$className = ltrim($usedOnType->getValue(), '\\');
		} else {
			if ($usedOnType->hasConstant($constant)->no()) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Cannot access constant %s on %s',
						$constant,
						$usedOnType->describe(VerbosityLevel::getRecommendedLevelByType($usedOnType))
					))->build(),
				];
			} else {
				$className = $usedOnType->getConstant($constant)->getDeclaringClass()->getDisplayName();
			}
		}
		$constant = $this->getFullyQualified($className, $constant);

		return $this->disallowedHelper->getDisallowedConstantMessage($constant, $scope, $displayName, $this->disallowedConstants);
	}


	private function getFullyQualified(string $class, string $constant): string
	{
		return "{$class}::{$constant}";
	}

}
