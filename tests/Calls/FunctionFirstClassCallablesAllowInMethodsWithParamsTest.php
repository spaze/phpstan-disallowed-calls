<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionFirstClassCallables>
 */
class FunctionFirstClassCallablesAllowInMethodsWithParamsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionFirstClassCallables(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => 'crc32()',
					'allowInMethods' => [
						'Fiction\Pulp\RoyaleAllowInFirstClassCallable::methodA()',
					],
					'allowParamsInAllowed' => [
						1 => 'a',
					],
				],
				[
					'function' => 'strtolower()',
					'allowInMethods' => [
						'Fiction\Pulp\RoyaleAllowInFirstClassCallable::methodA()',
					],
					'allowExceptParamsInAllowed' => [
						1 => 'a',
					],
				],
			]
		);
	}


	/**
	 * @requires PHP >= 8.1.0
	 */
	#[RequiresPhp('>= 8.1.0')]
	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/RoyaleAllowInFirstClassCallable.php'], [
			[
				'Calling crc32() is forbidden.',
				11,
			],
			[
				'Calling crc32() is forbidden.',
				18,
			],
			[
				'Calling strtolower() is forbidden.',
				19,
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
