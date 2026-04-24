<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

class FunctionCallsInvalidTypeStringConfigTest extends PHPStanTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	public function testInvalidTypeStringWithWildcard(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessageMatches("~foo\\(\\): Invalid typeString 'Foo\\\\\\*':.*Wildcards are not supported in typeString\\.~");
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
							'typeString' => 'Foo\*',
						],
					],
				],
			]
		);
	}


	/**
	 * @throws ShouldNotHappenException
	 */
	public function testInvalidTypeStringWithoutWildcard(): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage("foo(): Invalid typeString 'array<int':");
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
							'typeString' => 'array<int',
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
