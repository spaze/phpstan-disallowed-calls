<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class StaticCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new StaticCalls(
			[
				[
					'method' => 'Fiction\Pulp\Royale::withCheese()',
					'message' => 'a Quarter Pounder with Cheese?',
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
	}

}
