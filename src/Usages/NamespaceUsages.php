<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

/**
 * @implements Rule<Node>
 */
class NamespaceUsages implements Rule
{

	/** @var DisallowedNamespaceRuleErrors */
	private $disallowedNamespaceRuleErrors;

	/** @var list<DisallowedNamespace> */
	private $disallowedNamespace;

	/** @var Normalizer */
	private $normalizer;


	/**
	 * @param DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors
	 * @param DisallowedNamespaceFactory $disallowNamespaceFactory
	 * @param Normalizer $normalizer
	 * @param array<array{namespace?:string|list<string>, class?:string|list<string>, exclude?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $forbiddenNamespaces
	 */
	public function __construct(
		DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors,
		DisallowedNamespaceFactory $disallowNamespaceFactory,
		Normalizer $normalizer,
		array $forbiddenNamespaces
	) {
		$this->disallowedNamespaceRuleErrors = $disallowedNamespaceRuleErrors;
		$this->disallowedNamespace = $disallowNamespaceFactory->createFromConfig($forbiddenNamespaces);
		$this->normalizer = $normalizer;
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
		if ($node instanceof FullyQualified) {
			$description = 'Class';
			$namespaces = [$node->toString()];
		} elseif ($node instanceof NullableType && $node->type instanceof FullyQualified) {
			$description = 'Class';
			$namespaces = [$node->type->toString()];
		} elseif ($node instanceof UnionType || $node instanceof IntersectionType) {
			$description = 'Class';
			$namespaces = [];
			foreach ($node->types as $type) {
				if ($type instanceof FullyQualified) {
					$namespaces[] = $type->toString();
				}
			}
		} elseif ($node instanceof UseUse) {
			$namespaces = [$node->name->toString()];
		} elseif ($node instanceof StaticCall && $node->class instanceof Name) {
			$namespaces = [$node->class->toString()];
		} elseif ($node instanceof ClassConstFetch && $node->class instanceof Name) {
			$classReflection = $scope->resolveTypeByName($node->class)->getClassReflection();
			$description = $classReflection && $classReflection->isEnum() ? 'Enum' : 'Class';
			$namespaces = [$node->class->toString()];
		} elseif ($node instanceof Class_ && ($node->extends !== null || count($node->implements) > 0)) {
			$namespaces = [];

			if ($node->extends !== null) {
				$namespaces[] = $node->extends->toString();
			}

			foreach ($node->implements as $implement) {
				$namespaces[] = $implement->toString();
			}
		} elseif ($node instanceof New_ && $node->class instanceof Name) {
			$description = 'Class';
			$namespaces = [$node->class->toString()];
		} elseif ($node instanceof TraitUse) {
			$description = 'Trait';
			$namespaces = [];
			foreach ($node->traits as $trait) {
				$namespaces[] = $trait->toString();
			}
		} else {
			return [];
		}

		$errors = [];
		foreach ($namespaces as $namespace) {
			$errors = array_merge(
				$errors,
				$this->disallowedNamespaceRuleErrors->getDisallowedMessage($this->normalizer->normalizeNamespace($namespace), $description ?? 'Namespace', $scope, $this->disallowedNamespace)
			);
		}

		return $errors;
	}

}
