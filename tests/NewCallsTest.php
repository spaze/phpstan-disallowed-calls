<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class NewCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new NewCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'method' => 'Constructor\ClassWithConstructor::__construct()',
					'message' => 'class ClassWithConstructor should not be created',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
				],
				[
					'method' => 'Constructor\ClassWithoutConstructor::__construct()',
					'message' => 'class ClassWithoutConstructor should not be created',
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
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/src/disallowed/methodCalls.php'], [
			[
				'Calling Constructor\ClassWithConstructor::__construct() is forbidden, class ClassWithConstructor should not be created',
				32,
			],
			[
				'Calling Constructor\ClassWithoutConstructor::__construct() is forbidden, class ClassWithoutConstructor should not be created',
				34,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/src/disallowed-allow/functionCalls.php'], []);
	}

}
