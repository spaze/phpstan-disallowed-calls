<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Attributes\AttributeClass;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

class NamespaceUsagesTest extends RuleTestCase
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
					'namespace' => 'Framew*rk\Some*',
					'message' => 'no framework some',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'errorTip' => 'Work more on your frames',
				],
				[
					'namespace' => [
						'Inheritance\Base',
						'Inheritance\Sub',
					],
					'message' => 'no inheritance sub base',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Waldo\Quux\Blade',
					'message' => 'no blade',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'class' => 'Waldo\Foo\bar',
					'message' => 'no FooBar',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
						__DIR__ . '/../src/Bar.php', // Bar.php is analyzed below and has a class that extends Waldo\Foo\Bar, which would otherwise be reported
					],
				],
				[
					'namespace' => 'Traits\TestTrait',
					'message' => 'no TestTrait',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				// test disallowed paths
				[
					'namespace' => 'ZipArchive',
					'message' => 'use clippy instead of zippy',
					'disallowIn' => [
						__DIR__ . '/../src/disallowed/*.php',
					],
				],
				// test allowed instances
				[
					'namespace' => 'DateTimeZone',
					'allowInInstanceOf' => [
						'\Waldo\Foo\Bar',
						'Stringable',
					],
					'allowInUse' => true,
				],
				[
					'namespace' => 'DateTimeImmutable',
					'allowExceptInInstanceOf' => [
						'\Waldo\Foo\Bar',
						'Stringable',
					],
				],
				// test excluded attributes
				[
					'namespace' => 'PrivateModule\*',
					'message' => 'no private modules',
					'excludeWithAttribute' => [
						AttributeClass::class,
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/namespaceUsages.php'], [
			[
				// expect this error message:
				'Namespace Framework\SomeInterface is forbidden, no framework some. [Framework\SomeInterface matches Framew*rk\Some*]',
				// on this line:
				6,
				'Work more on your frames',
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base.',
				7,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no inheritance sub base.',
				8,
			],
			[
				'Namespace PrivateModule\PrivateClass is forbidden, no private modules. [PrivateModule\PrivateClass matches PrivateModule\*]',
				9,
			],
			// [
			//  'Namespace PrivateModule\PublicClass is forbidden, no private modules. [PrivateModule\PublicClass matches PrivateModule\*]',
			//  10,
			// ],
			[
				'Namespace Traits\TestTrait is forbidden, no TestTrait.',
				11,
			],
			[
				'Namespace Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				12,
			],
			[
				'Namespace Waldo\Quux\blade is forbidden, no blade. [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				13,
			],
			[
				'Namespace ZipArchive is forbidden, use clippy instead of zippy.',
				14,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base.',
				16,
			],
			[
				'Namespace Framework\SomeInterface is forbidden, no framework some. [Framework\SomeInterface matches Framew*rk\Some*]',
				16,
				'Work more on your frames',
			],
			[
				'Trait Traits\TestTrait is forbidden, no TestTrait.',
				19,
			],
			[
				'Class Waldo\Quux\blade is forbidden, no blade. [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				26,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no inheritance sub base.',
				34,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				40,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				46,
			],
			[
				'Class ZipArchive is forbidden, use clippy instead of zippy.',
				52,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base.',
				58,
			],
			// [
			//  'Class PrivateModule\PublicClass is forbidden, no private modules. [PrivateModule\PublicClass matches PrivateModule\*]',
			//  63,
			// ],
			[
				'Class PrivateModule\PrivateClass is forbidden, no private modules. [PrivateModule\PrivateClass matches PrivateModule\*]',
				68,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/namespaceUsages.php'], []);
	}


	public function testAllowInInstanceOf(): void
	{
		$this->analyse([__DIR__ . '/../src/Bar.php'], [
			[
				'Class DateTimeImmutable is forbidden.',
				32,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				35,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				41,
			],
			[
				'Class DateTimeZone is forbidden.',
				50,
			],
			[
				'Class DateTimeZone is forbidden.',
				54,
			],
			[
				'Class DateTimeZone is forbidden.',
				58,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				70,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				73,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				79,
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
