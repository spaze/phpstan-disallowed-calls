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
class AttributeUsagesAllowInMethodsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new AttributeUsages(
			$container->getByType(DisallowedAttributeRuleErrors::class),
			$container->getByType(DisallowedAttributeFactory::class),
			[
				[
					'attribute' => 'AttributeMethods\SomeAttribute',
					'allowInMethods' => [
						'*::action*()',
					],
				],
				[
					'attribute' => 'AttributeMethods\AnotherAttribute',
					'allowExceptInMethods' => [
						'*::forbidden*()',
					],
				],
				[
					'attribute' => 'AttributeMethods\FuncAttribute',
					'allowInFunctions' => [
						'AttributeMethods\allowed*()',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeMethods.php'], [
			[
				'Attribute AttributeMethods\SomeAttribute is forbidden.',
				26,
			],
			[
				'Attribute AttributeMethods\SomeAttribute is forbidden.',
				36,
			],
			[
				'Attribute AttributeMethods\SomeAttribute is forbidden.',
				42,
			],
			[
				'Attribute AttributeMethods\SomeAttribute is forbidden.',
				57,
			],
			[
				'Attribute AttributeMethods\AnotherAttribute is forbidden.',
				69,
			],
			[
				'Attribute AttributeMethods\FuncAttribute is forbidden.',
				95,
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
