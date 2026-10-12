<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Node\StaticMethodCallableNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * Reports on first class callable syntax for a static method of a disallowed class or namespace, like `Foo::bar(...)`.
 *
 * PHPStan doesn't pass these as static calls, other usages have a different rule, <code>NamespaceUsages</code>
 *
 * @implements Rule<StaticMethodCallableNode>
 */
class NamespaceFirstClassCallableUsages implements Rule
{

	private DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors;

	private NamespaceUsageFactory $namespaceUsageFactory;

	/** @var list<DisallowedNamespace> */
	private array $disallowedNamespace;


	/**
	 * @param DisallowedNamespaceRuleErrors $disallowedNamespaceRuleErrors
	 * @param DisallowedNamespaceFactory $disallowNamespaceFactory
	 * @param NamespaceUsageFactory $namespaceUsageFactory
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
		return StaticMethodCallableNode::class;
	}


	/**
	 * @param StaticMethodCallableNode $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$originalNode = $node->getOriginalNode();
		if (!$originalNode->class instanceof FullyQualified) {
			return [];
		}
		return $this->disallowedNamespaceRuleErrors->getDisallowedMessage(
			$originalNode,
			$this->namespaceUsageFactory->createClassUsage($originalNode->class, $scope),
			$scope,
			$this->disallowedNamespace,
		);
	}

}
