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
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

/**
 * Reports on class constant usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ClassConstFetch>
 */
class ClassConstantUsages implements Rule
{

	/** @var DisallowedConstantRuleErrors */
	private $disallowedConstantRuleErrors;

	/** @var TypeResolver */
	private $typeResolver;

	/** @var Formatter */
	private $formatter;

	/** @var list<DisallowedConstant> */
	private $disallowedConstants;


	/**
	 * @param DisallowedConstantRuleErrors $disallowedConstantRuleErrors
	 * @param DisallowedConstantFactory $disallowedConstantFactory
	 * @param TypeResolver $typeResolver
	 * @param Formatter $formatter
	 * @param array<array{class?:string, enum?:string, constant?:string|list<string>, case?:string|list<string>, message?:string, allowIn?:list<string>}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		DisallowedConstantRuleErrors $disallowedConstantRuleErrors,
		DisallowedConstantFactory $disallowedConstantFactory,
		TypeResolver $typeResolver,
		Formatter $formatter,
		array $disallowedConstants
	) {
		$this->disallowedConstantRuleErrors = $disallowedConstantRuleErrors;
		$this->typeResolver = $typeResolver;
		$this->formatter = $formatter;
		$this->disallowedConstants = $disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ClassConstFetch::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node instanceof ClassConstFetch)) {
			throw new ShouldNotHappenException(sprintf('$node should be %s but is %s', ClassConstFetch::class, get_class($node)));
		}
		if ($node->name instanceof Identifier) {
			return $this->getConstantRuleErrors($scope, (string)$node->name, $this->typeResolver->getType($node->class, $scope));
		}
		$type = $scope->getType($node->name);
		$errors = [];
		foreach ($type->getConstantStrings() as $constantString) {
			$errors = array_merge(
				$errors,
				$this->getConstantRuleErrors($scope, $constantString->getValue(), $this->typeResolver->getType($node->class, $scope))
			);
		}
		return $errors;
	}


	/**
	 * @param Scope $scope
	 * @param string $constant
	 * @param Type $type
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	private function getConstantRuleErrors(Scope $scope, string $constant, Type $type): array
	{
		if (strtolower($constant) === 'class') {
			return [];
		}

		$usedOnType = $type->getObjectTypeOrClassStringObjectType();
		$displayName = $usedOnType->getObjectClassNames() ? $this->getFullyQualified($usedOnType->getObjectClassNames(), $constant) : null;
		if ($usedOnType->getConstantStrings()) {
			$classNames = array_map(
				function (ConstantStringType $constantString): string {
					return $constantString->getValue();
				},
				$usedOnType->getConstantStrings()
			);
		} else {
			if ($usedOnType->hasConstant($constant)->yes()) {
				$classNames = [$usedOnType->getConstant($constant)->getDeclaringClass()->getDisplayName()];
			} elseif ($type->hasConstant($constant)->no()) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Cannot access constant %s on %s.',
						$constant,
						$type->describe(VerbosityLevel::getRecommendedLevelByType($type))
					))->build(),
				];
			} else {
				return [];
			}
		}
		return $this->disallowedConstantRuleErrors->get($this->getFullyQualified($classNames, $constant), $scope, $displayName, $this->disallowedConstants);
	}


	/**
	 * @param non-empty-list<string> $classNames
	 * @param string $constant
	 * @return string
	 */
	private function getFullyQualified(array $classNames, string $constant): string
	{
		return $this->formatter->formatIdentifier($classNames) . '::' . $constant;
	}

}
