<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PhpParser\Node;
use PhpParser\Node\Expr\ShellExec;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;

/**
 * Reports on dynamically using the execution backtick operator (<code>`ls`</code>).
 *
 * This class is in the Calls namespace because according to the docs,
 * "Use of the backtick operator is identical to shell_exec()"
 * https://www.php.net/operators.execution
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<ShellExec>
 */
class ShellExecCalls implements Rule
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
		return ShellExec::class;
	}


	/**
	 * @param ShellExec $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		return $this->disallowedHelper->getDisallowedMessage(
			null,
			$scope,
			'shell_exec',
			null,
			$this->disallowedCalls,
			'Using the backtick operator (`...`) is forbidden because shell_exec() is forbidden, %3$s%4$s'
		);
	}

}
