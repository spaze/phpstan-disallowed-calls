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

class AttributeUsagesTest extends RuleTestCase
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
		$this->analyse([__DIR__ . '/../src/disallowed-allow/ClassWithAttributesAllow.php'], []);

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

}
