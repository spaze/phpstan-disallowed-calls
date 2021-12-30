<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ConstantScalarType;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

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
	 * @param DisallowedCallFactory $disallowedCallFactory
	 * @param array $forbiddenCalls
	 * @phpstan-param ForbiddenCallsConfig $forbiddenCalls
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, DisallowedCallFactory $disallowedCallFactory, array $forbiddenCalls)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedCalls = $disallowedCallFactory->createFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return New_::class;
	}


	/**
	 * @param New_ $node
	 * @param Scope $scope
	 * @return RuleError[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($node->class instanceof Name) {
			$name = "{$node->class}::__construct";
		} elseif ($node->class instanceof Expr && $scope->getType($node->class) instanceof ConstantScalarType) {
			$name = $scope->getType($node->class)->getValue() . '::__construct';
		} else {
			return [];
		}
		return $this->disallowedHelper->getDisallowedMessage($node, $scope, $name, $name, $this->disallowedCalls);
	}

}
