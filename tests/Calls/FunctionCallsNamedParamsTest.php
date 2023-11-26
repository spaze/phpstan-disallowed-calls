<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

/**
 * @requires PHP > 8.0
 */
class FunctionCallsNamedParamsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$filePath = new FilePath(new FileHelper(__DIR__));
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath($filePath));
		return new FunctionCalls(
			new DisallowedCallsRuleErrors($allowed, new Identifier(), $filePath, $formatter),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			$this->createReflectionProvider(),
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
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden.',
				// on this line:
				12,
			],
			[
				// no required param
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				19,
			],
			[
				// missing $name + second param + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				20,
			],
			[
				// missing $name + second param + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				21,
			],
			[
				// missing $name + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				22,
			],
			[
				// missing $name + $path
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				23,
			],
			[
				// missing $name
				'Calling Foo\Bar\Waldo\bar() (as bar()) is forbidden.',
				24,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				29,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				30,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				31,
			],
			[
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				32,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				35,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				36,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				37,
			],
			[
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				38,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsNamedParams.php'], [
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden.',
				12,
			],
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden.',
				13,
			],
			[
				'Calling Foo\Bar\Waldo\foo() (as foo()) is forbidden.',
				14,
			],
			[
				// second param not 'VALUE'
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				29,
			],
			[
				// second param not 'VALUE'
				'Calling Foo\Bar\Waldo\baz() (as baz()) is forbidden.',
				31,
			],
			[
				// not $value param
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				35,
			],
			[
				// not $value param
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				36,
			],
			[
				// $value is not 'VALUE'
				'Calling Foo\Bar\Waldo\waldo() (as waldo()) is forbidden.',
				37,
			],
		]);
	}

}
