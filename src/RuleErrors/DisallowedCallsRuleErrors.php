<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr\CallLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;

class DisallowedCallsRuleErrors
{

	/** @var Allowed */
	private $allowed;

	/** @var Identifier */
	private $identifier;


	public function __construct(Allowed $allowed, Identifier $identifier)
	{
		$this->allowed = $allowed;
		$this->identifier = $identifier;
	}


	/**
	 * @param CallLike|null $node
	 * @param Scope $scope
	 * @param string $name
	 * @param string|null $displayName
	 * @param DisallowedCall[] $disallowedCalls
	 * @param string|null $message
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function get(?CallLike $node, Scope $scope, string $name, ?string $displayName, array $disallowedCalls, ?string $message = null): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			$callMatches = $this->identifier->matches($disallowedCall->getCall(), $name, $disallowedCall->getExcludes());
			if ($callMatches && !$this->allowed->isAllowed($scope, isset($node) ? $node->getArgs() : null, $disallowedCall)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					$message ?? 'Calling %s is forbidden, %s%s',
					($displayName && $displayName !== $name) ? "{$name}() (as {$displayName}())" : "{$name}()",
					$disallowedCall->getMessage(),
					$disallowedCall->getCall() !== $name ? " [{$name}() matches {$disallowedCall->getCall()}()]" : ''
				));
				if ($disallowedCall->getErrorIdentifier()) {
					$errorBuilder->identifier($disallowedCall->getErrorIdentifier());
				}
				if ($disallowedCall->getErrorTip()) {
					$errorBuilder->tip($disallowedCall->getErrorTip());
				}
				return [
					$errorBuilder->build(),
				];
			}
		}
		return [];
	}

}
