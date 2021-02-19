<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class StaticCallsTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new StaticCalls(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'method' => 'Fiction\Pulp\Royale::withCheese()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => '\Fiction\Pulp\*::withBad*()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => 'Fiction\Pulp\Royale::withoutCheese',
					'message' => 'a Quarter Pounder without Cheese?',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						1 => 1,
						2 => 2,
						3 => 3,
					],
					'allowParamsAnywhere' => [
						1 => 1,
						2 => 2,
						3 => 4,
					],
				],
				[
					'method' => 'Inheritance\Base::w*f*r()',
					'message' => 'method Base::woofer() is dangerous',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::z()',
					'message' => 'method TestTrait::z() is dangerous',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::zz()',
					'message' => 'method AnotherTestClass::zz() is dangerous',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Option::*()',
					'message' => 'do not use PhpOption',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Some::create()',
					'message' => 'do not use PhpOption',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\None::*()',
					'message' => 'do not use PhpOption',
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
		$this->analyse([__DIR__ . '/src/disallowed/staticCalls.php'], [
			[
				// expect this error message:
				'Calling Fiction\Pulp\Royale::withCheese() is forbidden, a Quarter Pounder with Cheese?',
				// on this line:
				7,
			],
			[
				'Calling Fiction\Pulp\Royale::withCheese() is forbidden, a Quarter Pounder with Cheese?',
				8,
			],
			[
				'Calling Fiction\Pulp\Royale::withBadCheese() is forbidden, a Quarter Pounder with Cheese? [Fiction\Pulp\Royale::withBadCheese() matches Fiction\Pulp\*::withBad*()]',
				9,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				12,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				14,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				18,
			],
			[
				'Calling Inheritance\Base::woofer() (as Inheritance\Sub::woofer()) is forbidden, method Base::woofer() is dangerous [Inheritance\Base::woofer() matches Inheritance\Base::w*f*r()]',
				28,
			],
			[
				'Calling Traits\TestTrait::z() (as Traits\TestClass::z()) is forbidden, method TestTrait::z() is dangerous',
				31,
			],
			[
				'Calling Traits\AnotherTestClass::zz() is forbidden, method AnotherTestClass::zz() is dangerous',
				32,
			],
			[
				'Calling PhpOption\Option::fromArraysValue() is forbidden, do not use PhpOption [PhpOption\Option::fromArraysValue() matches PhpOption\Option::*()]',
				35,
			],
			[
				'Calling PhpOption\None::create() is forbidden, do not use PhpOption [PhpOption\None::create() matches PhpOption\None::*()]',
				36,
			],
			[
				'Calling PhpOption\Some::create() is forbidden, do not use PhpOption',
				37,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/staticCalls.php'], [
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese?',
				18,
			],
		]);
	}

}
