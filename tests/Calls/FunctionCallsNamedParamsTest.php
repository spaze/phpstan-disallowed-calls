<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;

/**
 * @requires PHP > 8.0
 */
class FunctionCallsNamedParamsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new IsAllowedFileHelper(new FileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => 'Foo\Bar\Waldo\foo()',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						[
							'position' => 2,
							'name' => 'value',
						],
					],
					'allowParamsInAllowedAnyValue' => [
						[
							'position' => 4,
							'name' => 'path',
						],
					],
				],
				[
					'function' => 'Foo\Bar\Waldo\bar()',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						1,
						'name',
						2,
						'path',
					],
				],
				[
					'function' => 'Foo\Bar\Waldo\baz()',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						2 => 'VALUE',
					],
				],
				[
					'function' => 'Foo\Bar\Waldo\waldo()',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						'value' => 'VALUE',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsNamedParams.php'], [
			[
				// expect this error message:
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden, because reasons',
				// on this line:
				12,
			],
			[
				// no required param
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				19,
			],
			[
				// missing $name + second param + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				20,
			],
			[
				// missing $name + second param + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				21,
			],
			[
				// missing $name + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				22,
			],
			[
				// missing $name + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				23,
			],
			[
				// missing $name
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden, because reasons',
				24,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				29,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				30,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				31,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				32,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				35,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				36,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				37,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				38,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsNamedParams.php'], [
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden, because reasons',
				12,
			],
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden, because reasons',
				13,
			],
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden, because reasons',
				14,
			],
			[
				// second param not 'VALUE'
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				29,
			],
			[
				// second param not 'VALUE'
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden, because reasons',
				31,
			],
			[
				// not $value param
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				35,
			],
			[
				// not $value param
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				36,
			],
			[
				// $value is not 'VALUE'
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden, because reasons',
				37,
			],
		]);
	}

}
