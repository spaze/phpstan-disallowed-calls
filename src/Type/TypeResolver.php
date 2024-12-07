<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Type;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class TypeResolver
{

	/**
	 * @param Name|Expr $class
	 * @param Scope $scope
	 * @return Type
	 */
	public function getType($class, Scope $scope): Type
	{
		return $class instanceof Name ? new ObjectType($scope->resolveName($class)) : $scope->getType($class);
	}


	public function getVariableStringValue(Variable $variable, Scope $scope): ?string
	{
		$variableNameNode = $variable->name;
		$variableName = $variableNameNode instanceof String_ ? $variableNameNode->value : $variableNameNode;
		if (!is_string($variableName)) {
			return null;
		}
		$value = $scope->getVariableType($variableName)->getConstantScalarValues()[0];
		return is_string($value) ? $value : null;
	}

}
