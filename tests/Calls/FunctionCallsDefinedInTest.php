<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class FunctionCallsDefinedInTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => '\\Foo\\Bar\\Waldo\\f*()',
					'definedIn' => __DIR__ . '/../src/Fun*.php',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'function' => '\\Foo\\Bar\\Waldo\\b*()',
					'definedIn' => __DIR__ . '/../src/ThisFileDoesNotExist.php',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'function' => '\\Foo\\Bar\\Waldo\\q*x()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
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


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
