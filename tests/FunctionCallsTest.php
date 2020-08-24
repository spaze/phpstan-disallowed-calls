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
					'function' => 'var_dump()',
					'message' => 'use logger instead',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'function' => 'print_r()',
					'message' => 'nope',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'function' => 'printf()',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'function' => 'Foo\Bar\waldo()',
					'message' => 'whoa, a namespace',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/disallowed-calls.php'], [
			[
				'Calling var_dump() is forbidden, use logger instead',
				6,
			],
			[
				'Calling print_r() is forbidden, nope',
				7,
			],
			[
				'Calling printf() is forbidden, because reasons',
				8,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace',
				10,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace',
				11,
			],
		]);
		$this->analyse([__DIR__ . '/data/disallowed-calls-allowed.php'], []);
	}

}
