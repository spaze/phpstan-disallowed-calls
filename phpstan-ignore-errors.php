<?php
declare(strict_types = 1);

$config = [
	'parameters' => [
		'ignoreErrors' => [],
	]
];

if (PHP_VERSION_ID < 80000) {
	// https://github.com/phpstan/phpstan/discussions/12888
	$config['parameters']['ignoreErrors'][] = [
		'message' => '#^Call to method PHPStan\\\\Reflection\\\\ClassReflection::isEnum\\(\\) will always evaluate to false\\.$#',
		'reportUnmatched' => false,
	];
}

return $config;
