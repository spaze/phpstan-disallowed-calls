<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

class FunctionCallsInvalidClassPatternConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testEmptyClassPatternThrows(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage("foo(): classPattern is empty");
		$container = self::getContainer();
		new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => 'foo()',
					'disallowParamsInAllowed' => [
						1 => [
							'position' => 1,
							'classPattern' => '',
						],
					],
				],
			]
		);
	}


	/**
	 * @throws ShouldNotHappenException
	 */
	public function testBackslashOnlyClassPatternThrows(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage("foo(): classPattern is empty");
		$container = self::getContainer();
		new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => 'foo()',
					'disallowParamsInAllowed' => [
						1 => [
							'position' => 1,
							'classPattern' => '\\',
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
