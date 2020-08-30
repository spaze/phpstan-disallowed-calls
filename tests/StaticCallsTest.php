<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class StaticCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new StaticCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'method' => 'Fiction\Pulp\Royale::withCheese()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => 'Fiction\Pulp\Royale::withBadCheese()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => 'Fiction\Pulp\Royale::withoutCheese()',
					'message' => 'a Quarter Pounder without Cheese?',
					'allowIn' => [
						'data/*-allowed.php',
						'data/*-allowed.*',
					],
					'allowParamsInAllowed' => [
						1 => 1,
						2 => 2,
						3 => 3,
					],
					'allowParamsAnywhere' => [
						1 => 1,
						2 => 2,
						3 => 4,
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/disallowed-calls.php'], [
			[
				'Calling Fiction\Pulp\Royale::withCheese() is forbidden, a Quarter Pounder with Cheese?',
				14,
			],
			[
				'Calling Fiction\Pulp\Royale::withCheese() is forbidden, a Quarter Pounder with Cheese?',
				18,
			],
			[
				'Calling Fiction\Pulp\Royale::withBadCheese() is forbidden, a Quarter Pounder with Cheese?',
				20,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				21,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				23,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				26,
			],
		]);
		$this->analyse([__DIR__ . '/data/disallowed-calls-allowed.php'], [
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				27,
			],
		]);
	}

}
