<?php
declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/libs', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $fileInfo) {
	if ($fileInfo->getExtension() === 'php') {
		require_once $fileInfo->getPathname();
	}
}
