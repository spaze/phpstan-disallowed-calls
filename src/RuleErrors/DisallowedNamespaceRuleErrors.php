<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;

class DisallowedNamespaceRuleErrors
{

	/** @var AllowedPath */
	private $allowedPath;


	public function __construct(AllowedPath $allowedPath)
	{
		$this->allowedPath = $allowedPath;
	}


	/**
	 * @param string $namespace
	 * @param string $description
	 * @param Scope $scope
	 * @param DisallowedNamespace[] $disallowedNamespaces
	 * @return RuleError[]
	 */
	public function getDisallowedMessage(string $namespace, string $description, Scope $scope, array $disallowedNamespaces): array
	{
		foreach ($disallowedNamespaces as $disallowedNamespace) {
			if ($this->allowedPath->isAllowedPath($scope, $disallowedNamespace)) {
				continue;
			}

			if (!$this->matchesNamespace($disallowedNamespace->getNamespace(), $namespace)) {
				continue;
			}

			$errorBuilder = RuleErrorBuilder::message(sprintf(
				'%s %s is forbidden, %s%s',
				$description,
				$namespace,
				$disallowedNamespace->getMessage(),
				$disallowedNamespace->getNamespace() !== $namespace ? " [{$namespace} matches {$disallowedNamespace->getNamespace()}]" : ''
			));
			if ($disallowedNamespace->getErrorIdentifier()) {
				$errorBuilder->identifier($disallowedNamespace->getErrorIdentifier());
			}
			if ($disallowedNamespace->getErrorTip()) {
				$errorBuilder->tip($disallowedNamespace->getErrorTip());
			}
			return [
				$errorBuilder->build(),
			];
		}

		return [];
	}


	private function matchesNamespace(string $pattern, string $value): bool
	{
		if ($pattern === $value) {
			return true;
		}

		if (fnmatch($pattern, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
			return true;
		}

		return false;
	}

}
