<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper as PHPStanFileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class NamespaceUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new NamespaceUsages(
			new DisallowedNamespaceHelper(new FileHelper(new PHPStanFileHelper(__DIR__))),
			[
				[
					'namespace' => 'Framew*rk\Some*',
					'message' => 'no framework some',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Inheritance\Base',
					'message' => 'no inheritance Base',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Inheritance\Sub',
					'message' => 'no sub',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Waldo\Quux\Blade',
					'message' => 'no blade',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Waldo\Foo\Bar',
					'message' => 'no FooBar',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'namespace' => 'Traits\TestTrait',
					'message' => 'no TestTrait',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/src/disallowed/namespaceUsages.php'], [
			[
				// Based on the configuration above, in this file:
				'Namespace Framework\SomeInterface is forbidden, no framework some [Framework\SomeInterface matches Framew*rk\Some*]',
				// on this line:
				6,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance Base',
				7,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no sub',
				8,
			],
			[
				'Namespace Traits\TestTrait is forbidden, no TestTrait',
				9,
			],
			[
				'Namespace Waldo\Quux\Blade is forbidden, no blade',
				10,
			],
			[
				'Namespace Waldo\Foo\Bar is forbidden, no FooBar',
				11,
			],
			[
				'Namespace Inheritance\Base is forbidden, no inheritance Base',
				13,
			],
			[
				'Namespace Framework\SomeInterface is forbidden, no framework some [Framework\SomeInterface matches Framew*rk\Some*]',
				13,
			],
			[
				'Namespace Traits\TestTrait is forbidden, no TestTrait',
				17,
			],
			[
				'Namespace Waldo\Quux\Blade is forbidden, no blade',
				20,
			],
			[
				'Namespace Inheritance\Sub is forbidden, no sub',
				28,
			],
			[
				'Namespace Waldo\Foo\Bar is forbidden, no FooBar',
				34,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/namespaceUsages.php'], []);
	}

}
