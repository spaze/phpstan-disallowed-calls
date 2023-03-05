<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Type;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
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

}
