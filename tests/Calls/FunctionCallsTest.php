<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Waldo\Quux\Blade;

class FunctionCallsTest extends RuleTestCase
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
					'function' => '\var_dump()',
					'message' => 'use logger instead',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'errorTip' => 'See docs',
				],
				[
					'function' => 'print_r()',
					'message' => 'nope',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsAnywhere' => [
						2 => true,
					],
				],
				[
					'function' => 'printf',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'function' => '\Foo\Bar\waldo()',
					'message' => 'whoa, a namespace',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParamsInAllowed' => [
						1 => 123,
					],
				],
				[
					'function' => 'shell_*',
					'exclude' => [
						'shell_b*()',
					],
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				// test param overwriting
				[
					'function' => 'exe*()',
				],
				[
					'function' => 'exe*()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				// test disallowed param values
				[
					'function' => 'hash()',
					'message' => 'MD4 very bad',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						1 => 'md4',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'SHA-1 bad soon™',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						1 => 'sha1',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'MD5 bad',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						1 => 'MD5',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'SHA-1 bad SOON™',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						1 => 'SHA1',
					],
				],
				[
					'function' => 'setcookie()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsAnywhere' => [
						3 => 0,
					],
					'allowParamsInAllowed' => [
						4 => '/',
					],
				],
				[
					'function' => 'header()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						2,
					],
					'allowParamsInAllowedAnyValue' => [
						3,
					],
				],
				[
					'function' => 'htmlspecialchars()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamFlagsInAllowed' => [
						[
							'position' => 2,
							'name' => 'flags',
							'value' => ENT_QUOTES,
						],
					],
				],
				[
					'function' => 'array_filter()',
					'message' => 'callback parameter must be given.',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						2,
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\mocky()',
					'message' => 'mocking Blade is not allowed.',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						1 => Blade::class,
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\config()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						1 => 'string-key',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCalls.php'], [
			[
				// expect this error message:
				'Calling var_dump() is forbidden, use logger instead.',
				// on this line:
				7,
				'See docs',
			],
			[
				'Calling print_R() is forbidden, nope. [print_R() matches print_r()]',
				8,
			],
			[
				'Calling printf() is forbidden.',
				9,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				10,
			],
			[
				'Calling Foo\Bar\waldo() (as waldo()) is forbidden, whoa, a namespace.',
				11,
			],
			[
				'Calling shell_exec() is forbidden. [shell_exec() matches shell_*()]',
				12,
			],
			[
				'Calling exec() is forbidden. [exec() matches exe*()]',
				13,
			],
			[
				'Calling print_r() is forbidden, nope.',
				25,
			],
			[
				'Calling hash() is forbidden, MD4 very bad.',
				49,
			],
			[
				'Calling hash() is forbidden, MD5 bad.',
				50,
			],
			[
				'Calling hash() is forbidden, MD5 bad.',
				51,
			],
			[
				'Calling hash() is forbidden, SHA-1 bad soon™.',
				52,
			],
			[
				'Calling hash() is forbidden, SHA-1 bad SOON™.',
				53,
			],
			[
				'Calling setcookie() is forbidden.',
				59,
			],
			[
				'Calling header() is forbidden.',
				64,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				69,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				70,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				71,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				72,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				73,
			],
			[
				'Calling array_filter() is forbidden, callback parameter must be given.',
				76,
			],
			[
				'Calling Foo\Bar\Waldo\mocky() is forbidden, mocking Blade is not allowed.',
				83,
			],
			[
				'Calling Foo\Bar\Waldo\config() is forbidden.',
				91,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCalls.php'], [
			[
				'Calling Foo\Bar\waldo() (as waldo()) is forbidden, whoa, a namespace.',
				11,
			],
			[
				'Calling setcookie() is forbidden.',
				59,
			],
			[
				'Calling setcookie() is forbidden.',
				60,
			],
			[
				'Calling header() is forbidden.',
				64,
			],
			[
				'Calling header() is forbidden.',
				65,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				69,
			],
			[
				'Calling htmlspecialchars() is forbidden.',
				70,
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
