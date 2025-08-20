<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\HelperRules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Spaze\PHPStan\Rules\Disallowed\Allowed\GetAttributesWhenInSignature;

/**
 * @implements Rule<Function_>
 */
class SetCurrentFunctionNameHelperRule implements Rule
{

	private GetAttributesWhenInSignature $attributesWhenInSignature;


	public function __construct(GetAttributesWhenInSignature $attributesWhenInSignature)
	{
		$this->attributesWhenInSignature = $attributesWhenInSignature;
	}


	public function getNodeType(): string
	{
		return Function_::class;
	}


	/**
	 * @param Function_ $node
	 * @param Scope $scope
	 * @return array{}
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->namespacedName !== null) {
			$this->attributesWhenInSignature->setCurrentFunctionName($node->namespacedName);
		}
		return [];
	}

}
