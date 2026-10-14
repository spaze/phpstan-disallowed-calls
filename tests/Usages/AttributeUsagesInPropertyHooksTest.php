<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;

/**
 * @requires PHP >= 8.4.0
 * @extends RuleTestCase<AttributeUsages>
 */
#[RequiresPhp('>= 8.4.0')]
class AttributeUsagesInPropertyHooksTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new AttributeUsages(
			$container->getByType(DisallowedAttributeRuleErrors::class),
			$container->getByType(DisallowedAttributeFactory::class),
			[
				[
					'attribute' => 'PropertyHooks\DisallowedAttribute',
					'allowInMethodsWithAttributes' => [
						'PropertyHooks\AllowAttribute',
					],
				],
				[
					'attribute' => 'PropertyHooks\AllowedExceptAttribute',
					'allowExceptInMethodsWithAttributes' => [
						'PropertyHooks\DenyAttribute',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Attributes placed on a hook are allowed or disallowed by the other attributes on the hook, like on methods
		$this->analyse([__DIR__ . '/../src/PropertyHooks.php'], [
			[
				'Attribute PropertyHooks\DisallowedAttribute is forbidden.',
				54,
			],
			[
				'Attribute PropertyHooks\AllowedExceptAttribute is forbidden.',
				59,
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
