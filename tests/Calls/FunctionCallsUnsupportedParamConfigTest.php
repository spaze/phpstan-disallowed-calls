<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

class FunctionCallsUnsupportedParamConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testUnsupportedArrayInParamConfig(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage('{foo(),bar()}: Parameter #2 $definitelyNotScalar has an unsupported type array specified in configuration');
		$container = self::getContainer();
		new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[ /** @phpstan-ignore argument.type (The test tests a bad config, so a wrong "'key' => 'unsupported'" value is expected here) */
				[
					'function' => [
						'foo()',
						'bar()',
					],
					'disallowParams' => [
						1 => [
							'position' => 1,
							'name' => 'key',
							'value' => 'scalar',
						],
						2 => [
							'position' => 2,
							'name' => 'definitelyNotScalar',
							'value' => [
								'key' => 'unsupported',
							],
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
