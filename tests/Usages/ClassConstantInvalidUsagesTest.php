<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\FileHelper;

class ClassConstantInvalidUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ClassConstantUsages(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			new DisallowedConstantFactory(),
			[]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/invalid/constantUsages.php'], [
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
