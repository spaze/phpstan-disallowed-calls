<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

/**
 * Reports on dynamically calling a disallowed function.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<FuncCall>
 */
class FunctionCalls implements Rule
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
		return FuncCall::class;
	}


	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node->name instanceof Name)) {
			return [];
		}
		$namespacedName = $node->name->getAttribute('namespacedName');
		if ($namespacedName !== null && !($namespacedName instanceof Name)) {
			throw new ShouldNotHappenException();
		}
		$displayName = $node->name->getAttribute('originalName');
		if ($displayName !== null && !($displayName instanceof Name)) {
			throw new ShouldNotHappenException();
		}
		foreach ([$namespacedName, $node->name] as $name) {
			$message = $this->disallowedHelper->getDisallowedMessage($node, $scope, (string)$name, (string)($displayName ?? $node->name), $this->disallowedCalls);
			if ($message) {
				return $message;
			}
		}
		return [];
	}

}
