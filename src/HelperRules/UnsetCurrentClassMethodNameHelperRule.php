<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\HelperRules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use Spaze\PHPStan\Rules\Disallowed\Allowed\GetAttributesWhenInSignature;

/**
 * @implements Rule<InClassMethodNode>
 */
class UnsetCurrentClassMethodNameHelperRule implements Rule
{

	private GetAttributesWhenInSignature $attributesWhenInSignature;


	public function __construct(GetAttributesWhenInSignature $attributesWhenInSignature)
	{
		$this->attributesWhenInSignature = $attributesWhenInSignature;
	}


	public function getNodeType(): string
	{
		return InClassMethodNode::class;
	}


	/**
	 * @param InClassMethodNode $node
	 * @param Scope $scope
	 * @return array{}
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$this->attributesWhenInSignature->unsetCurrentClassMethodName();
		return [];
	}

}
