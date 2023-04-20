<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\MethodFormatter;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;

class FunctionCallsInMultipleNamespacesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		return new FunctionCalls(
			new DisallowedRuleErrors(new Allowed(new MethodFormatter(), new AllowedPath(new FileHelper(__DIR__)))),
			new DisallowedCallFactory(new IdentifierFormatter()),
			[
				[
					'function' => '__()',
					'message' => 'use MyNamespace\__ instead',
				],
				[
					'function' => 'MyNamespace\__()',
					'message' => 'ha ha ha nope',
				],
				[
					'function' => 'printf()',
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
				'Calling printf() is forbidden, because reasons',
				26,
			],
			[
				'Calling printf() is forbidden, because reasons',
				27,
			],
			[
				'Calling MyNamespace\__() (as alias()) is forbidden, ha ha ha nope',
				35,
			],
			[
				'Calling printf() is forbidden, because reasons',
				36,
			],
			[
				'Calling printf() is forbidden, because reasons',
				37,
			],
		]);
	}

}
