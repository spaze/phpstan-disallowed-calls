<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;

class MethodCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$ruleLevelHelper = new RuleLevelHelper($this->createBroker(), false, false, false);
		return new MethodCalls(
			$ruleLevelHelper,
			[
				[
					'method' => 'Waldo\Quux\Blade::runner()',
					'message' => "I've seen tests you people wouldn't believe",
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/disallowed-calls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe",
				25,
			],
		]);
	}

}
