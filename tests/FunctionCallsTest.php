<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class FunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			[
				[
					'function' => 'var_dump()',
					'message' => 'use logger instead',
				],
				[
					'function' => 'print_r()',
					'message' => 'nope',
				],
				[
					'function' => 'printf()',
				],
				[
					'function' => 'Foo\Bar\waldo()',
					'message' => 'whoa, a namespace',
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
	}

}
