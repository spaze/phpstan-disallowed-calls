<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Type;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class TypeResolver
{

	private Normalizer $normalizer;


	public function __construct(Normalizer $normalizer)
	{
		$this->normalizer = $normalizer;
	}


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
		$value = $scope->getVariableType($variableName)->getConstantScalarValues()[0] ?? null;
		return is_string($value) ? $value : null;
	}


	/**
	 * @param FuncCall|MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @return list<Name>
	 * @throws ShouldNotHappenException
	 */
	public function getNamesFromCall(Node $node, Scope $scope): array
	{
		if ($node->name instanceof Name) {
			$namespacedName = $node->name->getAttribute('namespacedName');
			if ($namespacedName !== null && !($namespacedName instanceof Name)) {
				throw new ShouldNotHappenException();
			}
			return $namespacedName !== null ? [$namespacedName, $node->name] : [$node->name];
		} elseif ($node->name instanceof String_) {
			return [new Name($this->normalizer->normalizeNamespace($node->name->value))];
		} elseif ($node->name instanceof Variable) {
			$value = $this->getVariableStringValue($node->name, $scope);
			if (!is_string($value)) {
				return [];
			}
			return [new Name($this->normalizer->normalizeNamespace($value))];
		} elseif ($node->name instanceof Identifier) {
			return [new Name($node->name->name)];
		}
		return [];
	}

}
