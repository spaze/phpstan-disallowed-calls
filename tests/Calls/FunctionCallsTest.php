<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;
use Stringable;
use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class FunctionCallsTest extends RuleTestCase
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
					'errorTip' => [
						'See docs',
						'Press F',
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
				// test allowed instances
				[
					'function' => 'simplexml_load_string',
					'allowInInstanceOf' => [
						Bar::class,
						Stringable::class,
					],
				],
				[
					'function' => 'dom_import_simplexml()',
					'disallowInInstanceOf' => [
						Bar::class,
						Stringable::class,
					],
				],
				// test allowInInstanceOf + allowExceptParamsInAllowed: allowed in hierarchy except when param is 'forbidden'
				[
					'function' => 'str_starts_with()',
					'allowInInstanceOf' => [
						'Waldo\Foo\BarBase',
					],
					'allowExceptParamsInAllowed' => [
						2 => 'forbidden',
					],
				],
				// test disallowInInstanceOf + allowExceptParamsInAllowed: disallowed in hierarchy only when param is 'forbidden'
				[
					'function' => 'str_ends_with()',
					'disallowInInstanceOf' => [
						'Waldo\Foo\BarBase',
					],
					'allowExceptParamsInAllowed' => [
						2 => 'forbidden',
					],
				],
				// test disallowInInstanceOf + allowParamsInAllowed: disallowed in hierarchy unless param is 'allowed_param'
				[
					'function' => 'str_contains()',
					'disallowInInstanceOf' => [
						'Waldo\Foo\BarBase',
					],
					'allowParamsInAllowed' => [
						2 => 'allowed_param',
					],
				],
				// test allowInInstanceOf + allowParamsInAllowed: allowed in hierarchy only when param is 'allowed_chars'
				[
					'function' => 'ltrim()',
					'allowInInstanceOf' => [
						'Waldo\Foo\BarBase',
					],
					'allowParamsInAllowed' => [
						2 => 'allowed_chars',
					],
				],
				// test allowed instances with wildcards, intentionally wrong case to test FNM_CASEFOLD
				[
					'function' => 'str_pad()',
					'allowInInstanceOf' => [
						'waldo\foo\wild*',
					],
				],
				[
					'function' => 'str_repeat()',
					'disallowInInstanceOf' => [
						'Waldo\Foo\Wild*',
					],
				],
				// test allowExceptIn + allowParamsInAllowed
				[
					'function' => 'pow()',
					'allowExceptIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
					],
					'allowParamsInAllowed' => [
						1 => 2,
					],
				],
				// test allowExceptIn + allowExceptParamsInAllowed
				[
					'function' => 'intdiv()',
					'allowExceptIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
					],
					'allowExceptParamsInAllowed' => [
						1 => 2,
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
				"• See docs\n• Press F",
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
			[
				'Calling print_r() is forbidden, nope.',
				102,
			],
			[
				'Calling print_r() is forbidden, nope.',
				103,
			],
			[
				'Calling print_r() is forbidden, nope.',
				106,
			],
			[
				'Calling Print_R() is forbidden, nope. [Print_R() matches print_r()]',
				107,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				110,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				111,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				114,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				115,
			],
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace.',
				117,
			],
			[
				'Calling Foo\Bar\Waldo() is forbidden, whoa, a namespace. [Foo\Bar\Waldo() matches Foo\Bar\waldo()]',
				118,
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


	public function testAllowInInstanceOf(): void
	{
		$this->analyse([__DIR__ . '/../src/Bar.php'], [
			[
				'Calling dom_import_simplexml() is forbidden.',
				38,
			],
			[
				'Calling simplexml_load_string() is forbidden.',
				56,
			],
			[
				'Calling dom_import_simplexml() is forbidden.',
				76,
			],
		]);
	}


	public function testAllowInInstanceOfWildcard(): void
	{
		$this->analyse([__DIR__ . '/../src/BarWildcard.php'], [
			[
				'Calling str_repeat() is forbidden.',
				16,
			],
			[
				'Calling str_repeat() is forbidden.',
				27,
			],
			[
				'Calling str_repeat() is forbidden.',
				38,
			],
			[
				'Calling str_repeat() is forbidden.',
				49,
			],
			[
				'Calling str_repeat() is forbidden.',
				60,
			],
			[
				'Calling str_pad() is forbidden.',
				70,
			],
		]);
	}


	public function testAllowExceptInWithParams(): void
	{
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsExceptWithParams.php'], [
			[
				'Calling pow() is forbidden.',
				6,
			],
			[
				'Calling intdiv() is forbidden.',
				10,
			],
		]);
	}


	public function testInstanceOfWithParams(): void
	{
		$this->analyse([__DIR__ . '/../src/BarInstanceOfWithParams.php'], [
			[
				'Calling str_starts_with() is forbidden.',
				11,
			],
			[
				'Calling str_ends_with() is forbidden.',
				13,
			],
			[
				'Calling str_contains() is forbidden.',
				16,
			],
			[
				'Calling str_starts_with() is forbidden.',
				26,
			],
			[
				'Calling str_ends_with() is forbidden.',
				28,
			],
			[
				'Calling str_contains() is forbidden.',
				31,
			],
			[
				'Calling str_starts_with() is forbidden.',
				42,
			],
			[
				'Calling str_starts_with() is forbidden.',
				43,
			],
			[
				'Calling ltrim() is forbidden.',
				59,
			],
			[
				'Calling ltrim() is forbidden.',
				70,
			],
			[
				'Calling ltrim() is forbidden.',
				71,
			],
			[
				'Calling ltrim() is forbidden.',
				82,
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
