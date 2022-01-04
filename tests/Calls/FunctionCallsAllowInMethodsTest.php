<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;

class FunctionCallsAllowInMethodsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new IsAllowedFileHelper(new FileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => 'md5_file()',
					'allowInMethods' => [
						'\\Fiction\\Pulp\\Royale::withB*dCheese()',
					],
				],
				[
					'function' => 'sha1_file()',
					'allowInFunctions' => [
						'\\Fiction\\Pulp\\Royale::WithoutCheese()',
					],
					'allowParamsInAllowed' => [
						2 => true,
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../libs/Royale.php'], []);
	}

}
