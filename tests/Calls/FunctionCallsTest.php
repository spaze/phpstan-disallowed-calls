<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\FileHelper;

class FunctionCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => '\var_dump()',
					'message' => 'use logger instead',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => 'print_r()',
					'message' => 'nope',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhere' => [
						2 => true,
					],
				],
				[
					'function' => 'printf',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => '\Foo\Bar\waldo()',
					'message' => 'whoa, a namespace',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptParamsInAllowed' => [
						1 => 123,
					],
				],
				[
					'function' => 'shell_*',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test param overwriting
				[
					'function' => 'exe*()',
				],
				[
					'function' => 'exe*()',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test disallowed param values
				[
					'function' => 'hash()',
					'message' => 'MD4 very bad',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						1 => 'md4',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'SHA-1 bad soon™',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						1 => 'sha1',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'MD5 bad',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						1 => 'MD5',
					],
				],
				[
					'function' => 'hash()',
					'message' => 'SHA-1 bad SOON™',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptCaseInsensitiveParams' => [
						1 => 'SHA1',
					],
				],
				[
					'function' => 'setcookie()',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
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
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						2,
					],
					'allowParamsInAllowedAnyValue' => [
						3,
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
				'Calling var_dump() is forbidden, use logger instead',
				// on this line:
				7,
			],
			[
				'Calling print_R() is forbidden, nope [print_R() matches print_r()]',
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
			[
				'Calling hash() is forbidden, MD4 very bad',
				49,
			],
			[
				'Calling hash() is forbidden, MD5 bad',
				50,
			],
			[
				'Calling hash() is forbidden, MD5 bad',
				51,
			],
			[
				'Calling hash() is forbidden, SHA-1 bad soon™',
				52,
			],
			[
				'Calling hash() is forbidden, SHA-1 bad SOON™',
				53,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				57,
			],
			[
				'Calling header() is forbidden, because reasons',
				62,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCalls.php'], [
			[
				'Calling Foo\Bar\waldo() is forbidden, whoa, a namespace',
				11,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				57,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				58,
			],
			[
				'Calling header() is forbidden, because reasons',
				62,
			],
			[
				'Calling header() is forbidden, because reasons',
				63,
			],
		]);
	}

}
