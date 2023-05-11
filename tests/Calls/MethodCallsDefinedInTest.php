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
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class MethodCallsDefinedInTest extends RuleTestCase
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
		return new MethodCalls(
			new DisallowedMethodRuleErrors(
				new DisallowedCallsRuleErrors($allowed, new Identifier(), $filePath),
				new TypeResolver(),
				$formatter
			),
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
				'Calling Waldo\Quux\Blade::andSorcery() is forbidden, because reasons [Waldo\Quux\Blade::andSorcery() matches *()]',
				// on this line:
				9,
			],
			[
				'Calling Waldo\Quux\Blade::server() is forbidden, because reasons [Waldo\Quux\Blade::server() matches *()]',
				10,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCallsDefinedIn.php'], []);
	}

}
