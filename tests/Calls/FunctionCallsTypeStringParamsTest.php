<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class FunctionCallsTypeStringParamsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => '\Foo\Bar\Waldo\config()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						[
							'position' => 1,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "array{foo:'bar'}",
						],
					],
					'allowParamsAnywhere' => [
						[
							'position' => 2,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "array{waldo:'baz', pine:'apple', 'orly':array{0, -1}}",
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\foo()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParamsInAllowed' => [
						[
							'position' => 1,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "'foo'|'pizza'",
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\bar()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						[
							'position' => 1,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "'bar'|'pub'",
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\baz()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						[
							'position' => 1,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "'inSensitive'|'Just a Little'",
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\mocky()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsAnywhere' => [
						[
							'position' => 1,
							'value' => 'is ignored when typeString is specified',
							'typeString' => "'moc'|'ky'",
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\arrayParam1()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => 'array{}',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\arrayParam2()',
					'allowParamsAnywhere' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => 'non-empty-array',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\intParam1()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamFlagsInAllowed' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => '2',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\intParam2()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamFlagsAnywhere' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => '2|8',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\intParam3()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParamFlagsInAllowed' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => '2|8',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\intParam4()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParamFlags' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => '2|8',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\mixedParam1()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParamsInAllowed' => [
						[
							'position' => 1,
							'name' => 'param',
							'value' => 'is ignored when typeString is specified',
							'typeString' => 'DateTimeInterface',
						],
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsTypeStringParams.php'], [
			[
				// expect this error message:
				'Calling Foo\Bar\Waldo\config() (as config()) is forbidden.',
				// on this line:
				6,
			],
			[
				'Calling Foo\Bar\Waldo\config() (as config()) is forbidden.',
				7,
			],
			[
				'Calling Foo\Bar\Waldo\foo() is forbidden.',
				9,
			],
			[
				'Calling Foo\Bar\Waldo\foo() is forbidden.',
				10,
			],
			[
				'Calling Foo\Bar\Waldo\bar() is forbidden.',
				11,
			],
			[
				'Calling Foo\Bar\Waldo\baz() is forbidden.',
				14,
			],
			[
				'Calling Foo\Bar\Waldo\arrayParam1() is forbidden.',
				15,
			],
			[
				'Calling Foo\Bar\Waldo\arrayParam2() is forbidden.',
				17,
			],
			[
				'Calling Foo\Bar\Waldo\mocky() is forbidden.',
				21,
			],
			[
				'Calling Foo\Bar\Waldo\intParam1() is forbidden.',
				22,
			],
			[
				'Calling Foo\Bar\Waldo\intParam1() is forbidden.',
				23,
			],
			[
				'Calling Foo\Bar\Waldo\intParam2() is forbidden.',
				25,
			],
			[
				'Calling Foo\Bar\Waldo\intParam3() is forbidden.',
				26,
			],
			[
				'Calling Foo\Bar\Waldo\intParam3() is forbidden.',
				27,
			],
			[
				'Calling Foo\Bar\Waldo\intParam4() is forbidden.',
				28,
			],
			[
				'Calling Foo\Bar\Waldo\intParam4() is forbidden.',
				29,
			],
			[
				'Calling Foo\Bar\Waldo\mixedParam1() is forbidden.',
				31,
			],
			[
				'Calling Foo\Bar\Waldo\mixedParam1() is forbidden.',
				32,
			],
			[
				'Calling Foo\Bar\Waldo\mixedParam1() is forbidden.',
				33,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsTypeStringParams.php'], [
			[
				'Calling Foo\Bar\Waldo\config() (as config()) is forbidden.',
				6,
			],
			[
				'Calling Foo\Bar\Waldo\foo() is forbidden.',
				9,
			],
			[
				'Calling Foo\Bar\Waldo\arrayParam2() is forbidden.',
				17,
			],
			[
				'Calling Foo\Bar\Waldo\intParam1() is forbidden.',
				23,
			],
			[
				'Calling Foo\Bar\Waldo\intParam3() is forbidden.',
				26,
			],
			[
				'Calling Foo\Bar\Waldo\mixedParam1() is forbidden.',
				31,
			],
			[
				'Calling Foo\Bar\Waldo\mixedParam1() is forbidden.',
				32,
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
