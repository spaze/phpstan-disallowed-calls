<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedPropertyFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedPropertyRuleErrors;

/**
 * @extends RuleTestCase<StaticPropertyUsages>
 */
class StaticPropertyUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new StaticPropertyUsages(
			$container->getByType(DisallowedPropertyFactory::class),
			$container->getByType(DisallowedPropertyRuleErrors::class),
			[
				[
					'property' => '\ClassWithProperties::$publicStaticProperty',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowInMethods' => [
						'\ClassWithProperties::okHere()',
					],
				],
				[
					'property' => '\ClassWithProperties::$privateStaticProperty',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => '\Traits\YetAnotherTrait::$publicStaticTra*Property',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => '\Traits\YetAnotherTrait::$publicStaticTraitProperty',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/propertyUsages.php'], [
			[
				// expect this error message:
				'Using Traits\YetAnotherTrait::$publicStaticTraitProperty (as ClassWithProperties::$publicStaticTraitProperty) is forbidden. [Traits\YetAnotherTrait::$publicStaticTraitProperty matches Traits\YetAnotherTrait::$publicStaticTra*Property]',
				// on this line:
				39,
			],
			[
				'Using ClassWithProperties::$publicStaticProperty is forbidden.',
				40,
			],
			[
				'Using ClassWithProperties::$privateStaticProperty is forbidden.',
				41,
			],
			[
				'Using ClassWithProperties::$publicStaticProperty is forbidden.',
				55,
			],
			[
				'Using Traits\YetAnotherTrait::$publicStaticTraitProperty (as ClassWithProperties::$publicStaticTraitProperty) is forbidden. [Traits\YetAnotherTrait::$publicStaticTraitProperty matches Traits\YetAnotherTrait::$publicStaticTra*Property]',
				58,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/propertyUsages.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
