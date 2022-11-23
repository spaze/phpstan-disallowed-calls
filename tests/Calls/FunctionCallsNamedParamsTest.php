<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;

/**
 * @requires PHP > 8.0
 */
class FunctionCallsNamedParamsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new IsAllowedFileHelper(new FileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => 'setcookie()',
					'allowIn' => [
						'../src/disallowed-allowed/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						'value',
					],
					'allowParamsInAllowedAnyValue' => [
						'path',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsNamedParams.php'], [
			[
				// expect this error message:
				'Calling setcookie() is forbidden, because reasons',
				// on this line:
				5,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				6,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				8,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				9,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsNamedParams.php'], [
			[
				'Calling setcookie() is forbidden, because reasons',
				5,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				6,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				7,
			],
			[
				'Calling setcookie() is forbidden, because reasons',
				8,
			],
		]);
	}

}
