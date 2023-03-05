<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

class NamespaceUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new NamespaceUsages(
			new DisallowedNamespaceRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedNamespaceFactory(),
			[
				[
					'namespace' => 'Framew*rk\Some*',
					'message' => 'no framework some',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
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
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Waldo\Quux\Blade',
					'message' => 'no blade',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'class' => 'Waldo\Foo\bar',
					'message' => 'no FooBar',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Traits\TestTrait',
					'message' => 'no TestTrait',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test disallowed paths
				[
					'namespace' => 'ZipArchive',
					'message' => 'use clippy instead of zippy',
					'disallowIn' => [
						'../src/disallowed/*.php',
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
				'Namespace Framework\SomeInterface is forbidden, no framework some [Framework\SomeInterface matches Framew*rk\Some*]',
				// on this line:
				6,
				'Work more on your frames',
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base',
				7,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no inheritance sub base',
				8,
			],
			[
				'Namespace Traits\TestTrait is forbidden, no TestTrait',
				9,
			],
			[
				'Namespace Waldo\Foo\Bar is forbidden, no FooBar [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				10,
			],
			[
				'Namespace Waldo\Quux\blade is forbidden, no blade [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				11,
			],
			[
				'Namespace ZipArchive is forbidden, use clippy instead of zippy',
				12,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance sub base',
				14,
			],
			[
				'Namespace Framework\SomeInterface is forbidden, no framework some [Framework\SomeInterface matches Framew*rk\Some*]',
				14,
				'Work more on your frames',
			],
			[
				'Trait Traits\TestTrait is forbidden, no TestTrait',
				17,
			],
			[
				'Class Waldo\Quux\blade is forbidden, no blade [Waldo\Quux\blade matches Waldo\Quux\Blade]',
				24,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no inheritance sub base',
				32,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				38,
			],
			[
				'Class Waldo\Foo\Bar is forbidden, no FooBar [Waldo\Foo\Bar matches Waldo\Foo\bar]',
				44,
			],
			[
				'Class ZipArchive is forbidden, use clippy instead of zippy',
				50,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/namespaceUsages.php'], []);
	}

}
