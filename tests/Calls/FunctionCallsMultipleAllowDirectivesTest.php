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
class FunctionCallsMultipleAllowDirectivesTest extends RuleTestCase
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
					'function' => 'md5()',
					'allowInInstanceOf' => [
						'\Attributes\Nowhere',
					],
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
					],
				],
				[
					'function' => 'sha1()',
					'allowParamsAnywhere' => [
						[
							'position' => 1,
							'value' => 'no-match',
						],
					],
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
					],
				],
				[
					'function' => 'strtolower()',
					'allowInClassWithAttributes' => [
						'\Attributes\Nowhere',
					],
					'allowInMethodsWithAttributes' => [
						'Attribute11',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeClass.php'], [
			[
				'Calling md5() is forbidden.',
				48,
			],
			[
				'Calling strtolower() is forbidden.',
				139,
			],
		]);
	}


	public function testRuleInFunctions(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeFunctions.php'], [
			[
				'Calling strtolower() is forbidden.',
				7,
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
