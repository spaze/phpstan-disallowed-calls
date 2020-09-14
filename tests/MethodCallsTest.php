<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class MethodCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new MethodCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'method' => 'Waldo\Quux\Blade::runner()',
					'message' => "I've seen tests you people wouldn't believe",
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
					'allowParamsInAllowed' => [
						1 => 42,
						2 => true,
						3 => '909',
					],
				],
				[
					'method' => 'Inheritance\Base::x()',
					'message' => 'method Base::x() is dangerous',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::x()',
					'message' => 'method TestTrait::x() is dangerous',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::y()',
					'message' => 'method AnotherTestClass::y() is dangerous',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'method' => 'Constructor\ClassWithConstructor::__construct()',
					'message' => 'Class ClassWithConstructor should not be created',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'method' => 'Constructor\ClassWithoutConstructor::__construct()',
					'message' => 'Class ClassWithoutConstructor should not be created',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/disallowed-calls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				33,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				35,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				36,
			],
			[
				'Calling Inheritance\Base::x() (as Inheritance\Sub::x()) is forbidden, method Base::x() is dangerous',
				46,
			],
			[
				'Calling Traits\TestTrait::x() (as Traits\TestClass::x()) is forbidden, method TestTrait::x() is dangerous',
				55,
			],
			[
				'Calling Traits\AnotherTestClass::y() is forbidden, method AnotherTestClass::y() is dangerous',
				57,
			],
			[
				'Calling Constructor\ClassWithConstructor::__construct() is forbidden, Class ClassWithConstructor should not be created',
				66,
			],
			[
				'Calling Constructor\ClassWithoutConstructor::__construct() is forbidden, Class ClassWithoutConstructor should not be created',
				67,
			],
		]);
		$this->analyse([__DIR__ . '/data/disallowed-calls-allowed.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				34,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				37,
			],
		]);
	}

}
