<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
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
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedCallsRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			$this->createReflectionProvider(),
			[
				[
					'function' => 'Foo\Bar\Waldo\foo()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
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
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
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
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						2 => 'VALUE',
					],
				],
				[
					'function' => 'Foo\Bar\Waldo\waldo()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
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


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
