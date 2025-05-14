<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;

/**
 * @extends RuleTestCase<AttributeUsages>
 */
class AttributeUsagesAllowInClassWithAttributesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new AttributeUsages(
			$container->getByType(DisallowedAttributeRuleErrors::class),
			$container->getByType(DisallowedAttributeFactory::class),
			[
				[
					'attribute' => '\Attributes\Attribute4',
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
					],
				],
				[
					'attribute' => '\Attributes\Attribute5',
					'disallowInClassWithAttributes' => [
						'\Attributes\Attribute3',
					],
				],
				[
					'attribute' => 'Attribute12',
					'allowInFunctionsWithAttributes' => [
						'Attribute10',
					],
				],
				[
					'attribute' => 'Attribute13',
					'allowExceptInFunctionsWithAttributes' => [
						'Attribute11',
					],
				],
				[
					'attribute' => 'Attribute7',
					'allowInClassWithMethodAttributes' => [
						'Attribute8',
					],
				],
				[
					'attribute' => 'Attribute8',
					'allowExceptInClassWithMethodAttributes' => [
						'Attribute7',
					],
				],
				[
					'attribute' => 'Attribute9',
					'allowInClassWithMethodAttributes' => [
						'Attribute7',
					],
				],
				[
					'attribute' => 'Attribute10',
					'allowExceptInClassWithMethodAttributes' => [
						'Attribute7',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeClass.php'], [
			[
				'Attribute Attributes\Attribute5 is forbidden.',
				32,
			],
			[
				'Attribute Attributes\Attribute4 is forbidden.',
				45,
			],
			[
				'Attribute Attribute8 is forbidden.',
				85,
			],
			[
				'Attribute Attribute8 is forbidden.',
				111,
			],
			[
				'Attribute Attribute10 is forbidden.',
				123,
			],
			[
				'Attribute Attribute13 is forbidden.',
				150,
			],
			[
				'Attribute Attribute12 is forbidden.',
				157,
			],
			[
				'Attribute Attribute13 is forbidden.',
				159,
			],
		]);
	}


	public function testRuleInFunctions(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeFunctions.php'], [
			[
				'Attribute Attribute13 is forbidden.',
				26,
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
