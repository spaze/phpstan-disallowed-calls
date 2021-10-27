<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\FileHelper;

class MethodCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new MethodCalls(
			new DisallowedHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'method' => 'Waldo\Quux\Blade::run*()',
					'message' => "I've seen tests you people wouldn't believe",
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						1 => 42,
						2 => true,
						3 => '909',
					],
				],
				[
					'method' => 'Inheritance\Base::x*()',
					'message' => 'Base::x*() methods are dangerous',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::*',
					'message' => 'all TestTrait methods are dangerous',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::zzTop()',
					'message' => 'method AnotherTestClass::zzTop() is dangerous',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\None::getIterator()',
					'message' => 'no PhpOption',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Some::getIterator()',
					'message' => 'no PhpOption',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test disallowed param values
				[
					'function' => 'DateTime::format()',
					'message' => 'why too kay',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						1 => 'y',
					],
				],
				[
					'function' => 'GeneratedCode\AccountQuery::execute*()',
					'message' => 'use another generated class',
					'allowCount' => 2,
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => 'GeneratedCode\AccountQuery::canBeCalledOnce()',
					'message' => 'use another generated class',
					'allowCount' => 1,
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => 'GeneratedCode\*Query::some*()',
					'message' => 'use another generated class',
					'allowCount' => 1,
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/methodCalls.php'], [
			[
				// expect this error message:
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				// on this line:
				12,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				13,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				16,
			],
			[
				'Calling Inheritance\Base::x() (as Inheritance\Sub::x()) is forbidden, Base::x*() methods are dangerous [Inheritance\Base::x() matches Inheritance\Base::x*()]',
				24,
			],
			[
				'Calling Traits\TestTrait::x() (as Traits\TestClass::x()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::x() matches Traits\TestTrait::*()]',
				28,
			],
			[
				'Calling Traits\TestTrait::y() (as Traits\AnotherTestClass::y()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::y() matches Traits\TestTrait::*()]',
				30,
			],
			[
				'Calling Traits\AnotherTestClass::zzTop() is forbidden, method AnotherTestClass::zzTop() is dangerous',
				31,
			],
			[
				'Calling PhpOption\None::getIterator() is forbidden, no PhpOption',
				48,
			],
			[
				'Calling PhpOption\Some::getIterator() is forbidden, no PhpOption',
				54,
			],
			[
				'Calling DateTime::format() is forbidden, why too kay',
				57,
			],
			[
				'Calling GeneratedCode\AccountQuery::execute() more than 2 times is forbidden, use another generated class [GeneratedCode\AccountQuery::execute() matches GeneratedCode\AccountQuery::execute*()]',
				64,
			],
			[
				'Calling GeneratedCode\AccountQuery::executeOrThrow() more than 2 times is forbidden, use another generated class [GeneratedCode\AccountQuery::executeOrThrow() matches GeneratedCode\AccountQuery::execute*()]',
				65,
			],
			[
				'Calling GeneratedCode\AccountQuery::canBeCalledOnce() more than once is forbidden, use another generated class',
				67,
			],
			[
				'Calling GeneratedCode\AccountQuery::something() more than once is forbidden, use another generated class [GeneratedCode\AccountQuery::something() matches GeneratedCode\*Query::some*()]',
				69,
			],
			[
				'Calling GeneratedCode\UserQuery::something() more than once is forbidden, use another generated class [GeneratedCode\UserQuery::something() matches GeneratedCode\*Query::some*()]',
				73,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCalls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				12,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				13,
			],
		]);
	}

}
