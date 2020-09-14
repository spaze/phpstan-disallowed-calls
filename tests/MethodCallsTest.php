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
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
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
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::x()',
					'message' => 'method TestTrait::x() is dangerous',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::y()',
					'message' => 'method AnotherTestClass::y() is dangerous',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/src/disallowed/methodCalls.php'], [
			[
				// expect this error message:
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				// on this line:
				9,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				10,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				13,
			],
			[
				'Calling Inheritance\Base::x() (as Inheritance\Sub::x()) is forbidden, method Base::x() is dangerous',
				21,
			],
			[
				'Calling Traits\TestTrait::x() (as Traits\TestClass::x()) is forbidden, method TestTrait::x() is dangerous',
				25,
			],
			[
				'Calling Traits\AnotherTestClass::y() is forbidden, method AnotherTestClass::y() is dangerous',
				27,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/methodCalls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				9,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				10,
			],
		]);
	}

}
