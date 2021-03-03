<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;

class DisallowedNamespaceHelper
{

	/** @var FileHelper */
	private $fileHelper;


	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


	/**
	 * @param Scope $scope
	 * @param DisallowedNamespace $disallowedNamespace
	 * @return boolean
	 */
	private function isAllowed(Scope $scope, DisallowedNamespace $disallowedNamespace): bool
	{
		foreach ($disallowedNamespace->getAllowIn() as $allowedPath) {
			$match = fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile());
			if ($match) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @param array<array{namespace:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $config
	 * @return DisallowedNamespace[]
	 */
	public function createDisallowedNamespacesFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			$disallowed = new DisallowedNamespace(
				$disallowed['namespace'],
				$disallowed['message'] ?? null,
				$disallowed['allowIn'] ?? []
			);
			$disallowedNamespaces[$disallowed->getNamespace()] = $disallowed;
		}
		return array_values($disallowedNamespaces);
	}


	/**
	 * @param string $namespace
	 * @param Scope $scope
	 * @param DisallowedNamespace[] $disallowedNamespaces
	 * @return string[]
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
				sprintf(
					'Namespace %s is forbidden, %s%s',
					$namespace,
					$disallowedNamespace->getMessage(),
					$disallowedNamespace->getNamespace() !== $namespace ? " [{$namespace} matches {$disallowedNamespace->getNamespace()}]" : ''
				),
			];
		}

		return [];
	}


	private function matchesNamespace(string $pattern, string $value): bool
	{
		if ($pattern === $value) {
			return true;
		}

		if (fnmatch($pattern, $value, FNM_NOESCAPE)) {
			return true;
		}

		return false;
	}

}
