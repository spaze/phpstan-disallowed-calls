<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeEntity;
use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;
use Waldo\Quux\Blade;

class AttributeUsagesAllowParamsMultipleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$formatter = new Formatter($normalizer);
		$allowed = new Allowed($formatter, $normalizer, new AllowedPath(new FilePath(new FileHelper(__DIR__))));
		return new AttributeUsages(
			new DisallowedAttributeRuleErrors($allowed, new Identifier(), $formatter),
			new DisallowedAttributeFactory($allowed, $normalizer),
			[
				[
					'attribute' => [
						AttributeEntity::class,
					],
					'allowIn' => [
						'../src/disallowed-allow/ClassWithAttributesAllow.php',
					],
					'allowParamsAnywhereAnyValue' => [
						[
							'position' => 1,
							'name' => 'repositoryClass',
						],
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
						'../src/disallowed-allow/ClassWithAttributesAllow.php',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/ClassWithAttributes.php'], [
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
				'Attribute Attributes\AttributeClass is forbidden.',
				40,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				42,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/ClassWithAttributesAllow.php'], [
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
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
				22,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				28,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden.',
				42,
			],
		]);
	}

}
