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

class FunctionCallsAllowInFunctionsTest extends RuleTestCase
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
					'function' => 'md*()',
					'allowInFunctions' => [
						'\\Foo\\Bar\\Waldo\\qu*x()',
					],
				],
				[
					'function' => 'sha*()',
					'allowExceptInFunctions' => [
						'\\Foo\\Bar\\Waldo\\fred()',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../libs/Functions.php'], [
			[
				// expect this error message:
				'Calling sha1() is forbidden, because reasons [sha1() matches sha*()]',
				// on this line:
				15,
			],
		]);
	}

}
