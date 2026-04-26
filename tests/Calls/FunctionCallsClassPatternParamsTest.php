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
class FunctionCallsClassPatternParamsTest extends RuleTestCase
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
					'function' => '\Foo\Bar\Waldo\classPatternAllowed()',
					'allowParamsAnywhere' => [
						[
							'position' => 1,
							'name' => 'param',
							'classPattern' => 'Date*',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\classPatternDisallowed()',
					'allowExceptParamsAnywhere' => [
						[
							'position' => 1,
							'classPattern' => 'Date*',
						],
					],
				],
				[
					'function' => '\Foo\Bar\Waldo\classPatternPrecedence()',
					'allowParamsAnywhere' => [
						[
							'position' => 1,
							'classPattern' => 'Date*',
							'typeString' => 'Exception',
						],
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsClassPatternParams.php'], [
			[
				'Calling Foo\Bar\Waldo\classPatternAllowed() is forbidden.',
				6,
			],
			[
				'Calling Foo\Bar\Waldo\classPatternDisallowed() is forbidden.',
				7,
			],
			[
				'Calling Foo\Bar\Waldo\classPatternDisallowed() is forbidden.',
				8,
			],
			[
				'Calling Foo\Bar\Waldo\classPatternAllowed() is forbidden.',
				10,
			],
			[
				'Calling Foo\Bar\Waldo\classPatternAllowed() is forbidden.',
				13,
			],
			[
				'Calling Foo\Bar\Waldo\classPatternPrecedence() is forbidden.',
				15,
			],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
