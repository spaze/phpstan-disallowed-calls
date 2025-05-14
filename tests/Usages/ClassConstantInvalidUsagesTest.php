<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ClassConstantUsages>
 */
class ClassConstantInvalidUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$service = self::getContainer()->getService('classConstantUsages');
		assert($service instanceof ClassConstantUsages);
		return $service;
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/invalid/constantUsages.php'], [
			[
				// expect this error message:
				'Cannot access constant GLITTER on string.',
				// on this line:
				6,
			],
			[
				'Cannot access constant COOKIE on string.',
				10,
			],
			[
				'Cannot access constant COOKIE on class-string.',
				14,
			],
			[
				'Cannot access constant FTC on class-string<DateTimeZone>.',
				24,
			],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
