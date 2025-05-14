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
class FunctionCallsAllowInClassWithAttributesTest extends RuleTestCase
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
					'allowInClassWithAttributes' => [
						'\Attribute',
					],
				],
				[
					'function' => 'sha1()',
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
						'\Attributes\Attribute3',
					],
				],
				[
					'function' => 'strlen()',
					'allowExceptInClassWithAttributes' => [
						'\Attributes\Attribute3',
					],
				],
				[
					'function' => 'strtolower()',
					'allowInMethodsWithAttributes' => [
						'Attribute10',
					],
				],
				[
					'function' => 'strtoupper()',
					'allowExceptInMethodsWithAttributes' => [
						'Attribute11',
					],
				],
				[
					'function' => 'var_dump()',
					'allowInClassWithMethodAttributes' => [
						'Attribute6',
					],
				],
				[
					'function' => 'print_r()',
					'allowExceptInClassWithMethodAttributes' => [
						'Attribute6',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeClass.php'], [
			[
				'Calling strlen() is forbidden.',
				35,
			],
			[
				'Calling md5() is forbidden.',
				48,
			],
			[
				'Calling var_dump() is forbidden.',
				78,
			],
			[
				'Calling print_r() is forbidden.',
				99,
			],
			[
				'Calling strtolower() is forbidden.',
				153,
			],
			[
				'Calling strtoupper() is forbidden.',
				154,
			],
		]);
	}


	public function testRuleInFunctions(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeFunctions.php'], [
			[
				'Calling strtolower() is forbidden.',
				15,
			],
			[
				'Calling strtoupper() is forbidden.',
				16,
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
