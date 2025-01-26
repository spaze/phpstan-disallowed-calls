<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

class NamespaceUsagesAllowInClassWithAttributesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceUsages(
			$container->getByType(DisallowedNamespaceRuleErrors::class),
			$container->getByType(DisallowedNamespaceFactory::class),
			$container->getByType(NamespaceUsageFactory::class),
			[
				[
					'namespace' => 'Waldo\Foo\Bar',
					'allowInClassWithAttributes' => [
						'\Attributes\Attribute2',
					],
				],
				[
					'class' => 'Waldo\Quux\Blade',
					'disallowInClassWithAttributes' => [
						'\Attributes\Attribute3',
					],
					'allowInUse' => true,
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/AttributeClass.php'], [
			[
				'Namespace Waldo\Foo\Bar is forbidden.',
				7,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				36,
			],
			[
				'Class Waldo\Quux\Blade is forbidden.',
				37,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				50,
			],
			[
				'Class Waldo\Foo\Bar is forbidden.',
				63,
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
