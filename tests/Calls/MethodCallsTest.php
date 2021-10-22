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
					'function' => 'Fiction\Pulp\GeneratedClass::execute*()',
					'message' => 'use another generated class',
					'allowCount' => 2,
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => 'Fiction\Pulp\GeneratedClass::canBeCalledOnce()',
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
				11,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				12,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				15,
			],
			[
				'Calling Inheritance\Base::x() (as Inheritance\Sub::x()) is forbidden, Base::x*() methods are dangerous [Inheritance\Base::x() matches Inheritance\Base::x*()]',
				23,
			],
			[
				'Calling Traits\TestTrait::x() (as Traits\TestClass::x()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::x() matches Traits\TestTrait::*()]',
				27,
			],
			[
				'Calling Traits\TestTrait::y() (as Traits\AnotherTestClass::y()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::y() matches Traits\TestTrait::*()]',
				29,
			],
			[
				'Calling Traits\AnotherTestClass::zzTop() is forbidden, method AnotherTestClass::zzTop() is dangerous',
				30,
			],
			[
				'Calling PhpOption\None::getIterator() is forbidden, no PhpOption',
				47,
			],
			[
				'Calling PhpOption\Some::getIterator() is forbidden, no PhpOption',
				53,
			],
			[
				'Calling DateTime::format() is forbidden, why too kay',
				56,
			],
			[
				'Calling Fiction\Pulp\GeneratedClass::execute() more than 2 times is forbidden, use another generated class [Fiction\Pulp\GeneratedClass::execute() matches Fiction\Pulp\GeneratedClass::execute*()]',
				63,
			],
			[
				'Calling Fiction\Pulp\GeneratedClass::executeOrThrow() more than 2 times is forbidden, use another generated class [Fiction\Pulp\GeneratedClass::executeOrThrow() matches Fiction\Pulp\GeneratedClass::execute*()]',
				64,
			],
			[
				'Calling Fiction\Pulp\GeneratedClass::canBeCalledOnce() more than once is forbidden, use another generated class',
				66,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCalls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				11,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				12,
			],
		]);
	}

}
