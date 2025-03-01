<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\HelperRules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Spaze\PHPStan\Rules\Disallowed\Allowed\GetAttributesWhenInSignature;

/**
 * @implements Rule<ClassMethod>
 */
class SetCurrentClassMethodNameHelperRule implements Rule
{

	private GetAttributesWhenInSignature $attributesWhenInSignature;


	public function __construct(GetAttributesWhenInSignature $attributesWhenInSignature)
	{
		$this->attributesWhenInSignature = $attributesWhenInSignature;
	}


	public function getNodeType(): string
	{
		return ClassMethod::class;
	}


	/**
	 * @param ClassMethod $node
	 * @param Scope $scope
	 * @return array{}
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($scope->isInClass()) {
			$this->attributesWhenInSignature->setCurrentClassMethodName($scope->getClassReflection()->getName(), $node->name->name);
		}
		return [];
	}

}
