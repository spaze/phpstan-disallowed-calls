<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceHelper;

/**
 * @implements Rule<Node>
 */
class NamespaceUsages implements Rule
{

	/** @var DisallowedNamespaceHelper */
	private $disallowedHelper;

	/** @var DisallowedNamespace[] */
	private $disallowedNamespace;


	/**
	 * @param DisallowedNamespaceHelper $disallowedNamespaceHelper
	 * @param array<array{namespace:string, message?:string, allowIn?:string[]}> $forbiddenNamespaces
	 */
	public function __construct(DisallowedNamespaceHelper $disallowedNamespaceHelper, array $forbiddenNamespaces)
	{
		$this->disallowedHelper  = $disallowedNamespaceHelper;
		$this->disallowedNamespace = $this->disallowedHelper->createDisallowedNamespacesFromConfig($forbiddenNamespaces);
	}


	public function getNodeType(): string
	{
		return Node::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node instanceof FullyQualified) {
			$namespaces = [$node->toString()];
		} elseif ($node instanceof UseUse) {
			$namespaces = [$node->name->toString()];
		} elseif ($node instanceof StaticCall && $node->class instanceof Name) {
			$namespaces = [$node->class->toString()];
		} elseif ($node instanceof ClassConstFetch && $node->class instanceof Name) {
			$namespaces = [$node->class->toString()];
		} elseif ($node instanceof Class_ && ($node->extends !== null || count($node->implements) > 0)) {
			$namespaces = [];

			if ($node->extends !== null) {
				$namespaces[] = $node->extends->toString();
			}

			foreach ($node->implements as $implement) {
				$namespaces[] = $implement->toString();
			}
		} elseif ($node instanceof TraitUse) {
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
				$this->disallowedHelper->getDisallowedMessage(ltrim($namespace, '\\'), $scope, $this->disallowedNamespace)
			);
		}

		return $errors;
	}

}
