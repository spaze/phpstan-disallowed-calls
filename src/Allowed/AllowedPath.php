<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use Spaze\PHPStan\Rules\Disallowed\Disallowed;

class AllowedPath
{

	/** @var FileHelper */
	private $fileHelper;

	/** @var string|null */
	private $allowInRootDir;


	public function __construct(FileHelper $fileHelper, ?string $allowInRootDir = null)
	{
		$this->fileHelper = $fileHelper;
		$this->allowInRootDir = $allowInRootDir !== null ? $this->fileHelper->normalizePath($fileHelper->absolutizePath($allowInRootDir)) : null;
	}


	/**
	 * Make path absolute unless it starts with a wildcard, then return as is.
	 *
	 * @param string $path
	 * @param string|null $allowInRootDir
	 * @return string
	 */
	private function absolutizePath(string $path, ?string $allowInRootDir): string
	{
		if (strpos($path, '*') === 0) {
			return $path;
		}

		if ($allowInRootDir !== null) {
			$path = rtrim($allowInRootDir, '/') . '/' . ltrim($path, '/');
		}
		return $this->fileHelper->normalizePath($this->fileHelper->absolutizePath($path));
	}


	public function matches(Scope $scope, string $allowedPath): bool
	{
		$file = $scope->getTraitReflection() ? $scope->getTraitReflection()->getFileName() : $scope->getFile();
		return $file !== null && fnmatch($this->absolutizePath($allowedPath, $this->allowInRootDir), $this->absolutizePath($file, null), FNM_NOESCAPE);
	}


	public function isAllowedPath(Scope $scope, Disallowed $disallowed): bool
	{
		foreach ($disallowed->getAllowIn() as $allowedPath) {
			if ($this->matches($scope, $allowedPath)) {
				return true;
			}
		}
		if ($disallowed->getAllowExceptIn()) {
			foreach ($disallowed->getAllowExceptIn() as $allowedExceptPath) {
				if ($this->matches($scope, $allowedExceptPath)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

}
