<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

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
	public function absolutizePath(string $path): string
	{
		if (strpos($path, '*') === 0) {
			return $path;
		}

		if ($this->allowInRootDir !== null) {
			$path = rtrim($this->allowInRootDir, '/') . '/' . ltrim($path, '/');
		}
		return $this->fileHelper->normalizePath($this->fileHelper->absolutizePath($path));
	}

}
