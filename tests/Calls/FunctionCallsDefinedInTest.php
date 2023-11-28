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

class FunctionCallsDefinedInTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$filePath = new FilePath(new FileHelper(__DIR__), __DIR__);
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath($filePath));
		return new FunctionCalls(
			new DisallowedCallsRuleErrors($allowed, new Identifier(), $filePath, $formatter),
			new DisallowedCallFactory($formatter, $normalizer, $allowed),
			$this->createReflectionProvider(),
			[
				[
					'function' => '\\Foo\\Bar\\Waldo\\f*()',
					'definedIn' => '../libs/Fun*.php',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => '\\Foo\\Bar\\Waldo\\b*()',
					'definedIn' => '../libs/ThisFileDoesNotExist.php',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'function' => '\\Foo\\Bar\\Waldo\\q*x()',
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
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsDefinedIn.php'], [
			[
				// expect this error message:
				'Calling Foo\Bar\Waldo\fred() is forbidden. [Foo\Bar\Waldo\fred() matches Foo\Bar\Waldo\f*()]',
				// on this line:
				5,
			],
			[
				'Calling Foo\Bar\Waldo\foo() is forbidden. [Foo\Bar\Waldo\foo() matches Foo\Bar\Waldo\f*()]',
				6,
			],
			[
				'Calling Foo\Bar\Waldo\quux() is forbidden. [Foo\Bar\Waldo\quux() matches Foo\Bar\Waldo\q*x()]',
				13,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsDefinedIn.php'], []);
	}

}
