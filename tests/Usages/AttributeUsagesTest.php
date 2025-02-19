<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeColumn;
use Attributes\AttributeEntity;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;

/**
 * @requires PHP >= 8.0
 */
#[RequiresPhp('>= 8.0')]
class AttributeUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new AttributeUsages(
			$container->getByType(DisallowedAttributeRuleErrors::class),
			$container->getByType(DisallowedAttributeFactory::class),
			[
				[
					'attribute' => [
						AttributeEntity::class,
					],
					'allowParamsAnywhereAnyValue' => [
						[
							'position' => 1,
							'name' => 'repositoryClass',
						],
					],
				],
				[
					'attribute' => '#[\Attributes\AttributeClass()]',
					'allowInInstanceOf' => [
						'\Waldo\Foo\Bar',
						'Stringable',
					],
				],
				[
					'attribute' => AttributeColumn::class,
					'message' => 'use `utc_datetime_immutable` instead.',
					'allowExceptCaseInsensitiveParams' => [
						[
							'name' => 'type',
							'position' => 2,
							'value' => 'datetime',
						],
						[
							'name' => 'type',
							'position' => 2,
							'value' => 'datetime_immutable',
						],
					],
				],
				// test allowed instances
				[
					'attribute' => '\Attributes\AttributeColumn2',
					'allowExceptInInstanceOf' => [
						'\Waldo\Foo\Bar',
						'Stringable',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/ClassWithAttributes.php'], [
			[
				// expect this error message:
				'Attribute Attributes\AttributeEntity is forbidden.',
				// on this line:
				8,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				12,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				15,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				18,
			],
			[
				'Attribute Attributes\AttributeColumn is forbidden, use `utc_datetime_immutable` instead.',
				21,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				43,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				45,
			],
		]);

		$this->analyse([__DIR__ . '/../src/AttributesEverywhere.php'], [
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				6,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				10,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				13,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				19,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				23,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				26,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				30,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				32,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				48,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				52,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				54,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				61,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				63,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				69,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				70,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				76,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				77,
			],
		]);
	}


	public function testAllowInInstanceOf(): void
	{
		$this->analyse([__DIR__ . '/../src/Bar.php'], [
			[
				'Attribute Attributes\AttributeColumn2 is forbidden.',
				34,
			],
			[
				'Attribute Attributes\AttributeClass is forbidden.',
				53,
			],
			[
				'Attribute Attributes\AttributeColumn2 is forbidden.',
				72,
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
