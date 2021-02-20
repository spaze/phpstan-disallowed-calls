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
use PHPStan\Type\VerbosityLevel;

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
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[]}> $disallowedConstants
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

		$classNames = [];

		$displayName = ($usedOnType instanceof TypeWithClassName ? $this->getFullyQualified($usedOnType->getClassName(), $constant) : null);
		if ($usedOnType instanceof ConstantStringType) {
			$classNames[] = ltrim($usedOnType->getValue(), '\\');
		} else {
			if ($usedOnType->hasConstant($constant)->no()) {
				return [
					sprintf(
						'Cannot access constant %s on %s',
						$constant,
						$usedOnType->describe(VerbosityLevel::getRecommendedLevelByType($usedOnType))
					),
				];
			} else {
				$classNames[] = $usedOnType->getConstant($constant)->getDeclaringClass()->getDisplayName();

				if ($usedOnType instanceof TypeWithClassName) {
					$classNames[] = $usedOnType->getClassName();
				}
			}
		}

		foreach ($classNames as $className) {
			$errors = $this->disallowedHelper->getDisallowedConstantMessage(
				$this->getFullyQualified($className, $constant),
				$scope,
				$displayName,
				$this->disallowedConstants
			);

			// return immeditely on error (to prevent duplicate errors)
			if ($errors !== []) {
				return $errors;
			}
		}

		return [];
	}


	private function getFullyQualified(string $class, string $constant): string
	{
		return "{$class}::{$constant}";
	}

}
