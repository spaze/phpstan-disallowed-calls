<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use DirectoryIterator;
use PHPStan\Testing\PHPStanTestCase;

class AllRulesRegisteredTest extends PHPStanTestCase
{

	private const RULES_DIRS = [
		'Calls',
		'Usages',
		'HelperRules',
	];


	public function testAllRulesRegistered(): void
	{
		foreach (self::RULES_DIRS as $directory) {
			foreach (new DirectoryIterator(__DIR__ . '/../src/' . $directory) as $file) {
				if ($file->getExtension() === 'php') {
					$class = sprintf('Spaze\PHPStan\Rules\Disallowed\%s\%s', $directory, $file->getBasename('.php'));
					if (!class_exists($class)) {
						continue;
					}
					$services = self::getContainer()->findServiceNamesByType($class);
					self::assertNotEmpty($services, "{$class} is not a registered rule");
				}
			}
		}
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../extension.neon',
		];
	}

}
