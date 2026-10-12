<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use Spaze\PHPStan\Rules\Disallowed\Allowed\UsagePosition;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeHintContextVisitor;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsage;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * @implements Rule<Node>
 */
class NamespaceUsages implements Rule
{

	private DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors;

	private NamespaceUsageFactory $namespaceUsageFactory;

	/** @var list<DisallowedNamespace> */
	private array $disallowedNamespace;


	/**
	 * @param DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors
	 * @param DisallowedNamespaceFactory $disallowNamespaceFactory
	 * @param array<array{namespace?:string|list<string>, class?:string|list<string>, exclude?:string|list<string>, excludeWithAttribute?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, allowInInstanceOf?:list<string>, allowExceptInInstanceOf?:list<string>, disallowInInstanceOf?:list<string>, allowInClassWithAttributes?:list<string>, allowExceptInClassWithAttributes?:list<string>, disallowInClassWithAttributes?:list<string>, allowInFunctionsWithAttributes?:list<string>, allowInMethodsWithAttributes?:list<string>, allowExceptInFunctionsWithAttributes?:list<string>, allowExceptInMethodsWithAttributes?:list<string>, disallowInFunctionsWithAttributes?:list<string>, disallowInMethodsWithAttributes?:list<string>, allowInClassWithMethodAttributes?:list<string>, allowExceptInClassWithMethodAttributes?:list<string>, disallowInClassWithMethodAttributes?:list<string>, allowInParamTypes?:bool, allowExceptInParamTypes?:bool, disallowInParamTypes?:bool, allowInReturnType?:bool, allowExceptInReturnType?:bool, disallowInReturnType?:bool, allowInUse?:bool, errorIdentifier?:string, errorTip?:string|list<string>}> $forbiddenNamespaces
	 */
	public function __construct(
		DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors,
		DisallowedNamespaceFactory $disallowNamespaceFactory,
		NamespaceUsageFactory $namespaceUsageFactory,
		array $forbiddenNamespaces
	) {
		$this->disallowedNamespaceRuleErrors = $disallowedNamespaceRuleErrors;
		$this->disallowedNamespace = $disallowNamespaceFactory->createFromConfig($forbiddenNamespaces);
		$this->namespaceUsageFactory = $namespaceUsageFactory;
	}


	public function getNodeType(): string
	{
		return Node::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$position = null;

		if ($node instanceof FullyQualified) {
			$namespaces = [$this->createClassUsage($node, $scope)];
			$position = $this->getPosition($node);
		} elseif ($node instanceof NullableType && $node->type instanceof FullyQualified) {
			$namespaces = [$this->createClassUsage($node->type, $scope)];
			$position = $this->getPosition($node);
		} elseif ($node instanceof UnionType || $node instanceof IntersectionType) {
			$namespaces = [];
			foreach ($node->types as $type) {
				if ($type instanceof FullyQualified) {
					$namespaces[] = $this->createClassUsage($type, $scope);
				}
			}
			$position = $this->getPosition($node);
		} elseif ($node instanceof UseUse) {
			$namespaces = [$this->namespaceUsageFactory->create($node->name->toString(), 'Namespace', ErrorIdentifiers::DISALLOWED_NAMESPACE, true)];
		} elseif ($node instanceof StaticCall && $node->class instanceof FullyQualified) {
			$namespaces = [$this->createClassUsage($node->class, $scope)];
		} elseif ($node instanceof Class_ && ($node->extends !== null || count($node->implements) > 0)) {
			$namespaces = [];
			if ($node->extends instanceof FullyQualified) {
				$namespaces[] = $this->createClassUsage($node->extends, $scope);
			}
			foreach ($node->implements as $implement) {
				if ($implement instanceof FullyQualified) {
					$namespaces[] = $this->createClassUsage($implement, $scope);
				}
			}
		} elseif ($node instanceof Interface_ && count($node->extends) > 0) {
			$namespaces = [];
			foreach ($node->extends as $extend) {
				if ($extend instanceof FullyQualified) {
					$namespaces[] = $this->createClassUsage($extend, $scope);
				}
			}
		} elseif ($node instanceof Enum_ && count($node->implements) > 0) {
			$namespaces = [];
			foreach ($node->implements as $implement) {
				if ($implement instanceof FullyQualified) {
					$namespaces[] = $this->createClassUsage($implement, $scope);
				}
			}
		} elseif ($node instanceof New_ && $node->class instanceof Name) {
			$namespaces = [$this->namespaceUsageFactory->create($node->class->toString(), 'Class', ErrorIdentifiers::DISALLOWED_CLASS)];
		} elseif ($node instanceof Instanceof_ && $node->class instanceof FullyQualified) {
			$namespaces = [$this->createClassUsage($node->class, $scope)];
		} elseif ($node instanceof TraitUse) {
			$namespaces = [];
			foreach ($node->traits as $trait) {
				$namespaces[] = $this->namespaceUsageFactory->create($trait->toString(), 'Trait', ErrorIdentifiers::DISALLOWED_TRAIT);
			}
		} else {
			return [];
		}

		$errors = [];
		foreach ($namespaces as $namespaceUsage) {
			$ruleErrors = $this->disallowedNamespaceRuleErrors->getDisallowedMessage(
				$node,
				$namespaceUsage,
				$scope,
				$this->disallowedNamespace,
				$position,
			);
			if ($ruleErrors) {
				$errors = array_merge($errors, $ruleErrors);
			}
		}

		return $errors;
	}


	private function createClassUsage(FullyQualified $name, Scope $scope): NamespaceUsage
	{
		$classReflection = $scope->resolveTypeByName($name)->getClassReflection();
		if ($classReflection !== null) {
			if ($classReflection->isEnum()) {
				return $this->namespaceUsageFactory->create($name->toString(), 'Enum', ErrorIdentifiers::DISALLOWED_ENUM);
			} elseif ($classReflection->isInterface()) {
				return $this->namespaceUsageFactory->create($name->toString(), 'Interface', ErrorIdentifiers::DISALLOWED_INTERFACE);
			} elseif ($classReflection->isTrait()) {
				return $this->namespaceUsageFactory->create($name->toString(), 'Trait', ErrorIdentifiers::DISALLOWED_TRAIT);
			}
		}
		return $this->namespaceUsageFactory->create($name->toString(), 'Class', ErrorIdentifiers::DISALLOWED_CLASS);
	}


	/**
	 * @return UsagePosition::*|null
	 */
	private function getPosition(Node $node): ?int
	{
		if ($node->getAttribute(TypeHintContextVisitor::ATTRIBUTE_IN_PARAM_TYPE)) {
			return UsagePosition::PARAM_TYPE;
		}
		if ($node->getAttribute(TypeHintContextVisitor::ATTRIBUTE_IN_RETURN_TYPE)) {
			return UsagePosition::RETURN_TYPE;
		}
		return null;
	}

}
