<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeEntity;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;

/**
 * @extends RuleTestCase<AttributeUsages>
 */
class AttributeUsagesAllowInPathTest extends RuleTestCase
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
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/ClassWithAttributes.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
