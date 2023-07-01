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
		$allowed = new Allowed(new Formatter($normalizer), $normalizer, new AllowedPath(new FilePath(new FileHelper(__DIR__))));
		return new AttributeUsages(
			new DisallowedAttributeRuleErrors($allowed, new Identifier()),
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
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/ClassWithAttributes.php'], [
			[
				// expect this error message:
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				// on this line:
				8,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/ClassWithAttributesAllow.php'], [
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				8,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				12,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				18,
			],
		]);
	}

}
