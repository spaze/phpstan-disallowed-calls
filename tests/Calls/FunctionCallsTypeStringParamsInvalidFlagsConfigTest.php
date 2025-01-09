<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

class FunctionCallsTypeStringParamsInvalidFlagsConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testException(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage("Foo\Bar\Waldo\intParam1(): Parameter #1 has an unsupported type string of 2|'bruh' specified in configuration");
		$container = self::getContainer();
		new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
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


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
