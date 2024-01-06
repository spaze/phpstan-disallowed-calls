<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class FunctionCallsAllowInMethodsTest extends RuleTestCase
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
					'function' => 'md5_file()',
					'allowInMethods' => [
						'\\Fiction\\Pulp\\Royale::withB*dCheese()',
					],
				],
				[
					'function' => 'sha1_file()',
					'allowInFunctions' => [
						'\\Fiction\\Pulp\\Royale::WithoutCheese()',
					],
					'allowParamsInAllowed' => [
						2 => true,
					],
				],
				[
					'function' => 'sha1()',
					'allowExceptInFunctions' => [
						'\\Fiction\\Pulp\\Royale::__construct()',
					],
				],
			],
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/Royale.php'], [
			[
				// expect this error message:
				'Calling sha1() is forbidden.',
				// on this line:
				11,
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
