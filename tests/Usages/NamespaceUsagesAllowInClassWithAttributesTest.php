<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Constructor\ClassWithConstructor;
use Constructor\ClassWithoutConstructor;
use PhpOption\None;
use PhpOption\Some;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * @extends RuleTestCase<NamespaceUsages>
 */
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
				[
					'class' => None::class,
					'allowInMethodsWithAttributes' => [
						'Attribute10',
						'Foo\Bar\NamespacedAttribute',
					],
					'allowInUse' => true,
				],
				[
					'class' => Some::class,
					'allowExceptInFunctionsWithAttributes' => [
						'Attribute11',
					],
					'allowInUse' => true,
				],
				[
					'class' => ClassWithConstructor::class,
					'allowInClassWithMethodAttributes' => [
						'Attribute6',
					],
					'allowInUse' => true,
				],
				[
					'class' => ClassWithoutConstructor::class,
					'allowExceptInClassWithMethodAttributes' => [
						'Attribute6',
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
			[
				'Class Constructor\ClassWithConstructor is forbidden.',
				80,
			],
			[
				'Class Constructor\ClassWithoutConstructor is forbidden.',
				101,
			],
			[
				'Class PhpOption\None is forbidden.',
				151,
			],
			[
				'Class PhpOption\Some is forbidden.',
				151,
			],
			[
				'Class PhpOption\None is forbidden.',
				151,
			],
			[
				'Class PhpOption\Some is forbidden.',
				151,
			],
			[
				'Class PhpOption\None is forbidden.',
				155,
			],
			[
				'Class PhpOption\Some is forbidden.',
				156,
			],
			[
				'Class PhpOption\None is forbidden.',
				189,
			],
		]);
		$this->analyse([__DIR__ . '/../src/Functions.php'], [
			[
				'Class PhpOption\None is forbidden.',
				108,
			],
			[
				'Class PhpOption\Some is forbidden.',
				108,
			],
			[
				'Class PhpOption\None is forbidden.',
				108,
			],
			[
				'Class PhpOption\Some is forbidden.',
				108,
			],
			[
				'Class PhpOption\None is forbidden.',
				110,
			],
			[
				'Class PhpOption\Some is forbidden.',
				111,
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
