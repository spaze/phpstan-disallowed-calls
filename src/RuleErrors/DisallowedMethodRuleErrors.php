<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\Formatter\MethodFormatter;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class DisallowedMethodRuleErrors
{

	/** @var DisallowedRuleErrors */
	private $disallowedRuleErrors;

	/** @var TypeResolver */
	private $typeResolver;

	/** @var IdentifierFormatter */
	private $identifierFormatter;

	/** @var MethodFormatter */
	private $methodFormatter;


	public function __construct(
		DisallowedRuleErrors $disallowedRuleErrors,
		TypeResolver $typeResolver,
		IdentifierFormatter $identifierFormatter,
		MethodFormatter $methodFormatter
	) {
		$this->disallowedRuleErrors = $disallowedRuleErrors;
		$this->typeResolver = $typeResolver;
		$this->identifierFormatter = $identifierFormatter;
		$this->methodFormatter = $methodFormatter;
	}


	/**
	 * @param Name|Expr $class
	 * @param MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @param DisallowedCall[] $disallowedCalls
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function get($class, CallLike $node, Scope $scope, array $disallowedCalls): array
	{
		if (!isset($node->name) || !($node->name instanceof Identifier)) {
			return [];
		}

		$calledOnType = $this->typeResolver->getType($class, $scope);
		if ($calledOnType->canCallMethods()->yes() && $calledOnType->hasMethod($node->name->name)->yes()) {
			$method = $calledOnType->getMethod($node->name->name, $scope);
			$classNames = $calledOnType->getObjectClassNames();
			if (count($classNames) === 0) {
				$calledAs = null;
			} else {
				$calledAs = $this->methodFormatter->getFullyQualified($this->identifierFormatter->format($classNames), $method);
			}

			foreach ($method->getDeclaringClass()->getTraits() as $trait) {
				if ($trait->hasMethod($method->getName())) {
					$declaredAs = $this->methodFormatter->getFullyQualified($trait->getDisplayName(), $method);
					$message = $this->disallowedRuleErrors->get($node, $scope, $declaredAs, $calledAs, $disallowedCalls);
					if ($message) {
						return $message;
					}
				}
			}
		} else {
			return [];
		}

		$declaredAs = $this->methodFormatter->getFullyQualified($method->getDeclaringClass()->getDisplayName(false), $method);
		return $this->disallowedRuleErrors->get($node, $scope, $declaredAs, $calledAs, $disallowedCalls);
	}

}
