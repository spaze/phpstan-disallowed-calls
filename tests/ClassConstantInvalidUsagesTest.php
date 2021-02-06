<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class ClassConstantInvalidUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ClassConstantUsages(new DisallowedHelper(new FileHelper(__DIR__)), []);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/src/invalid/constantUsages.php'], [
			[
				// expect this error message:
				'Cannot access constant GLITTER on string',
				// on this line:
				6,
			],
			[
				'Cannot access constant COOKIE on string',
				10,
			],
			[
				'Cannot access constant COOKIE on class-string',
				14,
			],
		]);
	}

}
