<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class FunctionCallsParamsMessagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedCallsRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			$this->createReflectionProvider(),
			$container->getByType(Normalizer::class),
			$container->getByType(TypeResolver::class),
			[
				[
					'function' => '\Foo\Bar\Waldo\config()',
					'message' => 'foo & bar',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParamsAnyValue' => [
						1,
						2,
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\config()',
					'message' => 'foo',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParamsAnyValue' => [
						1,
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\config()',
					'message' => 'nothing',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\bar()',
					'message' => 'foo & bar',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						1 => 'foo',
						2 => 'bar',
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\bar()',
					'message' => 'foo',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'disallowParams' => [
						1 => 'foo',
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\bar()',
					'message' => 'nothing',
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
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsParamsMessages.php'], [
			[
				// expect this error message:
				'Calling Foo\Bar\Waldo\config() is forbidden, nothing.',
				// on this line:
				5,
			],
			[
				'Calling Foo\Bar\Waldo\config() is forbidden, foo.',
				6,
			],
			[
				'Calling Foo\Bar\Waldo\config() is forbidden, foo & bar.',
				7,
			],
			[
				'Calling Foo\Bar\Waldo\bar() is forbidden, nothing.',
				8,
			],
			[
				'Calling Foo\Bar\Waldo\bar() is forbidden, foo.',
				9,
			],
			[
				'Calling Foo\Bar\Waldo\bar() is forbidden, foo & bar.',
				10,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/functionCallsParamsMessages.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
