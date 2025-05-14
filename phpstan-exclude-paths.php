<?php
declare(strict_types = 1);

$config = [
	'parameters' => [
		'excludePaths' => [
			'analyse' => [],
		],
	]
];

if (PHP_VERSION_ID < 80100) {
	$config['parameters']['excludePaths']['analyse'][] = __DIR__ . '/tests/Usages/ClassConstantEnumUsagesTest.php';
}

return $config;
