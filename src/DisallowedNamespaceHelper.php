<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

class DisallowedNamespaceHelper
{

	/** @var IsAllowedFileHelper */
	private $isAllowedFileHelper;


	public function __construct(IsAllowedFileHelper $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	/**
	 * @param Scope $scope
	 * @param DisallowedNamespace $disallowedNamespace
	 * @return bool
	 */
	private function isAllowed(Scope $scope, DisallowedNamespace $disallowedNamespace): bool
	{
		foreach ($disallowedNamespace->getAllowIn() as $allowedPath) {
			$match = fnmatch($this->isAllowedFileHelper->absolutizePath($allowedPath), $scope->getFile());
			if ($match) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @param string $namespace
	 * @param Scope $scope
	 * @param DisallowedNamespace[] $disallowedNamespaces
	 * @return RuleError[]
	 */
	public function getDisallowedMessage(string $namespace, Scope $scope, array $disallowedNamespaces): array
	{
		foreach ($disallowedNamespaces as $disallowedNamespace) {
			if ($this->isAllowed($scope, $disallowedNamespace)) {
				continue;
			}

			if (!$this->matchesNamespace($disallowedNamespace->getNamespace(), $namespace)) {
				continue;
			}

			return [
				RuleErrorBuilder::message(sprintf(
					'Namespace %s is forbidden, %s%s',
					$namespace,
					$disallowedNamespace->getMessage(),
					$disallowedNamespace->getNamespace() !== $namespace ? " [{$namespace} matches {$disallowedNamespace->getNamespace()}]" : ''
				))
					->identifier($disallowedNamespace->getErrorIdentifier())
					->build(),
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
