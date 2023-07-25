<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use FilesystemIterator;
use PHPStan\Testing\PHPStanTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BootstrapTest extends PHPStanTestCase
{

	public function testBootstrapRequiresAllLibs(): void
	{
		$allRequired = get_required_files();
		$notRequired = [];
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/libs', FilesystemIterator::SKIP_DOTS));
		foreach ($iterator as $fileInfo) {
			if ($fileInfo->getExtension() === 'php') {
				if (!in_array($this->getFileHelper()->normalizePath($fileInfo->getPathname()), $allRequired)) {
					$notRequired[] = $fileInfo->getPathname();
				}
			}
		}
		$this->assertEmpty($notRequired, 'Require these files in bootstrap.php: ' . implode(', ', $notRequired));
	}

}
