<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

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
			$container->getByType(DisallowedCallsRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			$this->createReflectionProvider(),
			[
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
			],
		);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
