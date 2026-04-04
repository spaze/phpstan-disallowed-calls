<?php
declare(strict_types = 1);

if (!\Composer\InstalledVersions::isInstalled('shipmonk/dead-code-detector')) {
	return [];
}

return [
	'includes' => [
		__DIR__ . '/vendor/shipmonk/dead-code-detector/rules.neon',
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
