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
use PHPStan\Type\VerbosityLevel;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
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

	/** @var IdentifierFormatter */
	private $identifierFormatter;

	/** @var DisallowedConstant[] */
	private $disallowedConstants;


	/**
	 * @param DisallowedConstantRuleErrors $disallowedConstantRuleErrors
	 * @param DisallowedConstantFactory $disallowedConstantFactory
	 * @param TypeResolver $typeResolver
	 * @param IdentifierFormatter $identifierFormatter
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[]}> $disallowedConstants
	 * @throws ShouldNotHappenException
	 */
	public function __construct(
		DisallowedConstantRuleErrors $disallowedConstantRuleErrors,
		DisallowedConstantFactory $disallowedConstantFactory,
		TypeResolver $typeResolver,
		IdentifierFormatter $identifierFormatter,
		array $disallowedConstants
	) {
		$this->disallowedConstantRuleErrors = $disallowedConstantRuleErrors;
		$this->typeResolver = $typeResolver;
		$this->identifierFormatter = $identifierFormatter;
		$this->disallowedConstants = $disallowedConstantFactory->createFromConfig($disallowedConstants);
	}


	public function getNodeType(): string
	{
		return ClassConstFetch::class;
	}


	/**
	 * @param Node $node
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
		$usedOnType = $this->typeResolver->getType($node->class, $scope);

		if (strtolower($constant) === 'class') {
			return [];
		}

		$displayName = $usedOnType->getObjectClassNames() ? $this->getFullyQualified($usedOnType->getObjectClassNames(), $constant) : null;
		if ($usedOnType->getConstantStrings()) {
			$classNames = array_map(
				function (ConstantStringType $constantString): string {
					return ltrim($constantString->getValue(), '\\');
				},
				$usedOnType->getConstantStrings()
			);
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
				$classNames = [$usedOnType->getConstant($constant)->getDeclaringClass()->getDisplayName()];
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
		return $this->identifierFormatter->format($classNames) . '::' . $constant;
	}

}
