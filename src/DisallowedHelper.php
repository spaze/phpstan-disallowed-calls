<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;

class DisallowedHelper
{

	/** @var FileHelper */
	private $fileHelper;


	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


	/**
	 * @param string $file
	 * @param string[] $config
	 * @return boolean
	 */
	public function isAllowed(string $file, array $config): bool
	{
		foreach (($config['allowIn'] ?? []) as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $file)) {
				return true;
			}
		}
		return false;
	}

}
