<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\DisallowedHelper;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;

class FunctionCallsInMultipleNamespacesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedHelper(new IsAllowedFileHelper(new FileHelper(__DIR__))),
			new DisallowedCallFactory(),
			[
				[
					'function' => '__()',
					'message' => 'use MyNamespace\__ instead',
				],
				[
					'function' => 'MyNamespace\__()',
					'message' => 'ha ha ha nope',
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../libs/FunctionInMultipleNamespaces.php'], [
			[
				// expect this error message:
				'Calling __() (as alias()) is forbidden, use MyNamespace\__ instead',
				// on this line:
				18,
			],
			[
				'Calling MyNamespace\__() (as __()) is forbidden, ha ha ha nope',
				23,
			],
			[
				'Calling MyNamespace\__() (as alias()) is forbidden, ha ha ha nope',
				32,
			],
		]);
	}

}
