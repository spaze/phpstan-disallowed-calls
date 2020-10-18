<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class EvalCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new EvalCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'function' => 'eval()',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/src/disallowed/functionCalls.php'], [
			[
				'Calling eval() is forbidden, because reasons',
				28,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/src/disallowed-allow/functionCalls.php'], []);
	}

}
