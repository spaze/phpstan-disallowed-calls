<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class FunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'function' => '\var_dump()',
					'message' => 'use logger instead',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'function' => 'print_r()',
					'message' => 'nope',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
					'allowParamsAnywhere' => [
						2 => true,
					]
				],
				[
					'function' => 'printf',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'function' => '\Foo\Bar\waldo()',
					'message' => 'whoa, a namespace',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'function' => 'shell_*',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'function' => 'exe*()',
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
				// expect this error message:
				'Calling var_dump() is forbidden, use logger instead',
				// on this line:
				7,
			],
			[
				'Calling print_r() is forbidden, nope',
				8,
			],
			[
				'Calling printf() is forbidden, because reasons',
				9,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace',
				10,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace',
				11,
			],
			[
				'Calling shell_exec() is forbidden, because reasons [shell_exec() matches shell_*()]',
				12,
			],
			[
				'Calling exec() is forbidden, because reasons [exec() matches exe*()]',
				13,
			],
			[
				'Calling print_r() is forbidden, nope',
				25,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/src/disallowed-allow/functionCalls.php'], []);
	}

}
