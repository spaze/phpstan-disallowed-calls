<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;

class PrintCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		return new PrintCalls(
			new DisallowedRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => 'print()',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCalls.php'], [
			[
				'Calling print() is forbidden, because reasons',
				43,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCalls.php'], []);
	}

}
