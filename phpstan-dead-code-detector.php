<?php
declare(strict_types = 1);

$rulesNeon = __DIR__ . '/vendor/shipmonk/dead-code-detector/rules.neon';
// Can't use InstalledVersions::isInstalled() because here the InstalledVersions class comes from phpstan.phar, not this project's vendor/
if (!file_exists($rulesNeon)) {
	return [];
}

return [
	'includes' => [
		$rulesNeon,
	],
	'parameters' => [
		'shipmonkDeadCode' => [
			'usageExcluders' => [
				'tests' => [
					'enabled' => true,
				],
			],
			'detect' => [
				'deadEnumCases' => true,
			],
		],
		'ignoreErrors' => [
			[
				'identifier' => 'shipmonk.deadMethod', // Used in extension.neon
				'paths' => [
					__DIR__ . '/src/DisallowedSuperglobalFactory.php',
					__DIR__ . '/src/DisallowedKeywordFactory.php',
				],
			],
		],
	],
];
