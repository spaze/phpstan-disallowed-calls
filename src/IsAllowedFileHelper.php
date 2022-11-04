<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;

class IsAllowedFileHelper
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
	 * @return string
	 */
	private function absolutizePath(string $path): string
	{
		if (strpos($path, '*') === 0) {
			return $path;
		}

		if ($this->allowInRootDir !== null) {
			$path = rtrim($this->allowInRootDir, '/') . '/' . ltrim($path, '/');
		}
		return $this->fileHelper->normalizePath($this->fileHelper->absolutizePath($path));
	}


	public function matches(Scope $scope, string $allowedPath): bool
	{
		$file = $scope->getTraitReflection() ? $scope->getTraitReflection()->getFileName() : $scope->getFile();
		return $file !== null && fnmatch($this->absolutizePath($allowedPath), $file);
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
