<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class FunctionCallsTypeStringParamsInvalidFlagsConfigTest extends RuleTestCase
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
			[
				[
					'function' => '\Foo\Bar\Waldo\intParam1()',
					'allowParamFlagsAnywhere' => [
						[
							'position' => 1,
							'typeString' => "2|'bruh'",
						],
					],
				],
			]
		);
	}


	public function testException(): void
	{
		$this->expectException(UnsupportedParamTypeInConfigException::class);
		$this->expectExceptionMessage("Parameter #1 has an unsupported type string of 2|'bruh' specified in configuration");
		$this->analyse([__DIR__ . '/../src/disallowed/functionCallsTypeStringParams.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
