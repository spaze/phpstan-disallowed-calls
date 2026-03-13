<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedPropertyFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedPropertyRuleErrors;

/**
 * @extends RuleTestCase<InstancePropertyUsages>
 */
class InstancePropertyUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new InstancePropertyUsages(
			$container->getByType(DisallowedPropertyFactory::class),
			$container->getByType(DisallowedPropertyRuleErrors::class),
			[
				[
					'property' => 'Fiction\Pulp\Royale::$whopper',
					'message' => 'not sold here',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => 'DateInterval::d',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowInMethods' => [
						'\ClassWithProperties::okHere()',
					],
				],
				[
					'property' => '\Inheritance\Base::property',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => 'Traits\YetAnotherTrait::$public*Property',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => '\Traits\YetAnotherTrait::$protectedTraitProperty',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'property' => '\ClassWithProperties::$privateProperty',
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
				'Using Fiction\Pulp\Royale::$whopper is forbidden, not sold here.',
				// on this line:
				8,
			],
			[
				'Using Fiction\Pulp\Royale::$whopper is forbidden, not sold here.',
				10,
			],
			[
				'Using Fiction\Pulp\Royale::$whopper is forbidden, not sold here.',
				13,
			],
			[
				'Using DateInterval::$d is forbidden.',
				15,
			],
			[
				'Using Inheritance\Base::$property (as Inheritance\Sub::$property) is forbidden.',
				18,
			],
			[
				'Using Traits\YetAnotherTrait::$publicTraitProperty (as Traits\TestClass::$publicTraitProperty) is forbidden. [Traits\YetAnotherTrait::$publicTraitProperty matches Traits\YetAnotherTrait::$public*Property]',
				21,
			],
			[
				'Using Traits\YetAnotherTrait::$publicTraitProperty (as ClassWithProperties::$publicTraitProperty) is forbidden. [Traits\YetAnotherTrait::$publicTraitProperty matches Traits\YetAnotherTrait::$public*Property]',
				36,
			],
			[
				'Using Traits\YetAnotherTrait::$protectedTraitProperty (as ClassWithProperties::$protectedTraitProperty) is forbidden.',
				37,
			],
			[
				'Using ClassWithProperties::$privateProperty is forbidden.',
				38,
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
