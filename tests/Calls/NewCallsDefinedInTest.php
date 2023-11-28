<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class NewCallsDefinedInTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$filePath = new FilePath(new FileHelper(__DIR__), __DIR__ . '/..');
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath($filePath));
		return new NewCalls(
			new DisallowedCallsRuleErrors($allowed, new Identifier(), $filePath, $formatter),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			[
				[
					'method' => '*',
					'definedIn' => 'libs/Bl*',
					'allowIn' => [
						'src/disallowed-allow/*.php',
						'src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/methodCallsDefinedIn.php'], [
			[
				// expect this error message:
				'Calling Waldo\Quux\Blade::__construct() is forbidden. [Waldo\Quux\Blade::__construct() matches *()]',
				// on this line:
				9,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCallsDefinedIn.php'], []);
	}

}
