<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeEntity;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;
use Waldo\Quux\Blade;

/**
 * @extends RuleTestCase<AttributeUsages>
 */
class AttributeUsagesAllowParamsInAllowedTest extends RuleTestCase
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
					'allowIn' => [
						__DIR__ . '/../src/ClassWithAttributes.php',
					],
					'allowParamsInAllowed' => [
						[
							'position' => 1,
							'name' => 'repositoryClass',
							'value' => Blade::class,
						],
					],
				],
				[
					'attribute' => '#[\Attributes\AttributeClass()]',
					'allowIn' => [
						__DIR__ . '/../src/ClassWithAttributes.php',
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
				'Attribute Attributes\AttributeEntity is forbidden.',
				25,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				31,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				45,
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
