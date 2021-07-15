<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class ExitDieCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ExitDieCalls(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			[
				[
					'function' => 'die()',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'function' => 'exit()',
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
				'Calling die() is forbidden, because reasons',
				30,
			],
			[
				'Calling die() is forbidden, because reasons',
				33,
			],
			[
				'Calling exit() is forbidden, because reasons',
				36,
			],
			[
				'Calling exit() is forbidden, because reasons',
				39,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/src/disallowed-allow/functionCalls.php'], []);
	}

}
