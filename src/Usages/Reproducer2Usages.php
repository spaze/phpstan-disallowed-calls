<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node>
 */
class Reproducer2Usages implements Rule
{

	public function getNodeType(): string
	{
		return FullyQualified::class;
	}


	/**
	 * @param FullyQualified $node
	 * @param Scope $scope
	 * @return list<RuleError>
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return [
			RuleErrorBuilder::message(sprintf('class %s found in method %s', $node->toString(), $scope->getFunction()?->getName() ?? 'null'))->build(),
		];
	}

}
