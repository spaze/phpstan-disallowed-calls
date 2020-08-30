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
			$this->createBroker(),
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
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/disallowed-calls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				32,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				34,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				35,
			],
		]);
		$this->analyse([__DIR__ . '/data/disallowed-calls-allowed.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				33,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				36,
			],
		]);
	}

}
