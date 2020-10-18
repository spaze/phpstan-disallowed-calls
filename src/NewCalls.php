<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ConstantScalarType;

/**
 * Reports on creating objects (calling constructors).
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<New_>
 */
class NewCalls implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var DisallowedCall[] */
	private $disallowedCalls;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param array<array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $forbiddenCalls
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedCalls = $this->disallowedHelper->createCallsFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return New_::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var New_ $node */
		if ($node->class instanceof Name) {
			$name = "{$node->class}::__construct";
		} elseif ($node->class instanceof Expr && $scope->getType($node->class) instanceof ConstantScalarType) {
			$name = $scope->getType($node->class)->getValue() . '::__construct';
		} else {
			return [];
		}
		return $this->disallowedHelper->getDisallowedMessage(null, $scope, $name, $name, $this->disallowedCalls);
	}

}
