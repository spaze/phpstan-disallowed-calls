<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

/**
 * Reports on statically calling a disallowed method or two.
 *
 * Dynamic calls have a different rule, <code>MethodCalls</code>
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<StaticCall>
 */
class StaticCalls implements Rule
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
		return StaticCall::class;
	}


	/**
	 * @param StaticCall $node
	 * @param Scope $scope
	 * @return RuleError[]
	 * @throws ClassNotFoundException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var StaticCall $node */
		return $this->disallowedHelper->getDisallowedMethodMessage($node->class, $node, $scope, $this->disallowedCalls);
	}

}
