<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Foo\Bar\AttributeEntity;
use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedAttributeFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\MethodFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedAttributeRuleErrors;

class AttributeUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new AttributeUsages(
			new DisallowedAttributeRuleErrors(new Allowed(new MethodFormatter(), new AllowedPath(new FileHelper(__DIR__)))),
			new DisallowedAttributeFactory(),
			[
				[
					'attribute' => [
						AttributeEntity::class,
					],
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsAnywhereAnyValue' => [
						[
							'position' => 1,
							'name' => 'repositoryClass',
						],
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/attributeUsages.php'], [
			[
				// expect this error message:
				'Attribute Foo\Bar\AttributeEntity is forbidden, because reasons',
				// on this line:
				8,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/attributeUsages.php'], []);
	}

}
