<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\Type;

/**
 * Reports on dynamically calling a forbidden method or two.
 *
 * Static calls have a different rule, <code>StaticCalls</code>
 *
 * Specify required arguments in a config file, example:
 * <code>
 * arguments:
 *   forbiddenCalls:
 *     -
 *       method: 'Tracy\ILogger::log()'
 *       message: 'use our own logger instead'
 *       allowIn:
 *         - optional/path/to/*.tests.php
 *         - another/file.php
 *       allowParamsInAllowed:
 *         1: 'foo'
 *         2: true
 *       allowParamsAnywhere:
 *         2: true
 *     -
 *       method: 'Foo\Bar::baz()'
 *       message: 'waldo instead'
 * </code>
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class MethodCalls implements Rule
{

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var string[][] */
	private $forbiddenCalls;


	public function __construct(Broker $broker, DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->ruleLevelHelper = new RuleLevelHelper($broker, true, false, true);
		$this->disallowedHelper = $disallowedHelper;
		$this->forbiddenCalls = $forbiddenCalls;
	}


	public function getNodeType(): string
	{
		return MethodCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var MethodCall $node */
		if (!($node->name instanceof Identifier)) {
			return [];
		}

		$name = $node->name->name;
		$typeResult = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			sprintf('Call to method %s() on an unknown class %%s.', $name),
			static function (Type $type) use ($name): bool {
				return $type->canCallMethods()->yes() && $type->hasMethod($name)->yes();
			}
		);

		foreach ($typeResult->getReferencedClasses() as $referencedClass) {
			$fullyQualified = current($typeResult->getReferencedClasses()) . "::{$name}()";
			foreach ($this->forbiddenCalls as $forbiddenCall) {
				if ($fullyQualified === $forbiddenCall['method'] && !$this->disallowedHelper->isAllowed($scope->getFile(), $node->args, $forbiddenCall)) {
					return [
						sprintf('Calling %s is forbidden, %s', $fullyQualified, $forbiddenCall['message'] ?? 'because reasons'),
					];
				}
			}
		}

		return [];
	}

}
