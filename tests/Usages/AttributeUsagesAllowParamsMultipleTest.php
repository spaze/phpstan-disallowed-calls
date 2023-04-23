<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeEntity;
use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;
use Waldo\Quux\Blade;

class AttributeUsagesAllowParamsMultipleTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$allowed = new Allowed(new Formatter(), new Normalizer(), new AllowedPath(new FileHelper(__DIR__)));
		return new AttributeUsages(
			new DisallowedAttributeRuleErrors($allowed),
			new DisallowedAttributeFactory($allowed),
			[
				[
					'attribute' => [
						AttributeEntity::class,
					],
					'allowIn' => [
						'../libs/ClassWithAttributesAllow.php',
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
		$this->analyse([__DIR__ . '/../libs/ClassWithAttributes.php'], [
			[
				// expect this error message:
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				// on this line:
				10,
			],
		]);
		$this->analyse([__DIR__ . '/../libs/ClassWithAttributesAllow.php'], [
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				10,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				15,
			],
			[
				'Attribute Attributes\AttributeEntity is forbidden, because reasons',
				22,
			],
		]);
	}

}
