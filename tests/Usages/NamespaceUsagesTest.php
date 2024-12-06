<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

class NamespaceUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceUsages(
			$container->getByType(DisallowedNamespaceRuleErrors::class),
			$container->getByType(DisallowedNamespaceFactory::class),
			$container->getByType(Normalizer::class),
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
				'Namespace Traits\TestTrait is forbidden, no TestTrait.',
				9,
			],
			[
				'Namespace Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				10,
			],
			[
				'Namespace Waldo\Quux\blade is forbidden, no blade. [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				11,
			],
			[
				'Namespace ZipArchive is forbidden, use clippy instead of zippy.',
				12,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base.',
				14,
			],
			[
				'Namespace Framework\SomeInterface is forbidden, no framework some. [Framework\SomeInterface matches Framew*rk\Some*]',
				14,
				'Work more on your frames',
			],
			[
				'Trait Traits\TestTrait is forbidden, no TestTrait.',
				17,
			],
			[
				'Class Waldo\Quux\blade is forbidden, no blade. [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				24,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no inheritance sub base.',
				32,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				38,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar. [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				44,
			],
			[
				'Class ZipArchive is forbidden, use clippy instead of zippy.',
				50,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base.',
				56,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/namespaceUsages.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
