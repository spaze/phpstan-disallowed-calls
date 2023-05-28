<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PHPStan\Analyser\Scope;
use Spaze\PHPStan\Rules\Disallowed\Disallowed;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;

class AllowedPath
{

	/** @var FilePath */
	private $filePath;


	public function __construct(FilePath $filePath)
	{
		$this->filePath = $filePath;
	}


	public function matches(Scope $scope, string $allowedPath): bool
	{
		$file = $scope->getTraitReflection() ? $scope->getTraitReflection()->getFileName() : $scope->getFile();
		return $file !== null && $this->filePath->fnMatch($allowedPath, $file);
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
