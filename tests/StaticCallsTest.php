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
		]);
		$this->analyse([__DIR__ . '/data/disallowed-calls-allowed.php'], []);
	}

}
