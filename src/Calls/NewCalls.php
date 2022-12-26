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
use PHPStan\Type\Constant\ConstantStringType;
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
	private const CONSTRUCT = '::__construct';

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
			$className = $node->class;
		} elseif ($node->class instanceof Expr) {
			$type = $scope->getType($node->class);
			if ($type instanceof ConstantStringType) {
				$className = new Name($type->getValue());
			}
		}
		if (!isset($className)) {
			return [];
		}

		$type = $scope->resolveTypeByName($className);
		$names = [
			$type->getClassName(),
		];
		$reflection = $type->getClassReflection();
		if ($reflection) {
			foreach ($reflection->getParents() as $parent) {
				$names[] = $parent->getName();
			}
			foreach ($reflection->getInterfaces() as $interface) {
				$names[] = $interface->getName();
			}
		}

		$errors = [];
		foreach ($names as $name) {
			$name .= self::CONSTRUCT;
			$errors = array_merge(
				$errors,
				$this->disallowedHelper->getDisallowedMessage($node, $scope, $name, $type->getClassName() . self::CONSTRUCT, $this->disallowedCalls)
			);
		}

		return $errors;
	}

}
