<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper as PHPStanFileHelper;

class FileHelper
{

	/** @var PHPStanFileHelper */
	private $fileHelper;


	public function __construct(PHPStanFileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
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

		return $this->fileHelper->absolutizePath($path);
	}

}
