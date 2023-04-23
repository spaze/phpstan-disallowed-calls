<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class StaticCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$formatter = new Formatter();
		return new StaticCalls(
			new DisallowedMethodRuleErrors(
				new DisallowedRuleErrors(new Allowed($formatter, new AllowedPath(new FileHelper(__DIR__)))),
				new TypeResolver(),
				$formatter
			),
			new DisallowedCallFactory($formatter),
			[
				[
					'method' => 'Fiction\Pulp\Royale::withCheese()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => '\Fiction\Pulp\*::withBad*()',
					'message' => 'a Quarter Pounder with Cheese?',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [],
				],
				[
					'method' => 'Fiction\Pulp\Royale::WithoutCheese',
					'message' => 'a Quarter Pounder without Cheese?',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
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
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::z()',
					'message' => 'method TestTrait::z() is dangerous',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::zz()',
					'message' => 'method AnotherTestClass::zz() is dangerous',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Option::*()',
					'message' => 'do not use PhpOption',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Some::create()',
					'message' => 'do not use PhpOption',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\None::*()',
					'message' => 'do not use PhpOption',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/staticCalls.php'], [
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
				'Calling Fiction\Pulp\Royale::WithBadCheese() is forbidden, a Quarter Pounder with Cheese? [Fiction\Pulp\Royale::WithBadCheese() matches Fiction\Pulp\*::withBad*()]',
				9,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese? [Fiction\Pulp\Royale::withoutCheese() matches Fiction\Pulp\Royale::WithoutCheese()]',
				12,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese? [Fiction\Pulp\Royale::withoutCheese() matches Fiction\Pulp\Royale::WithoutCheese()]',
				14,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese? [Fiction\Pulp\Royale::withoutCheese() matches Fiction\Pulp\Royale::WithoutCheese()]',
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
		$this->analyse([__DIR__ . '/../src/disallowed-allow/staticCalls.php'], [
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese? [Fiction\Pulp\Royale::withoutCheese() matches Fiction\Pulp\Royale::WithoutCheese()]',
				18,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese? [Fiction\Pulp\Royale::withoutCheese() matches Fiction\Pulp\Royale::WithoutCheese()]',
				21,
			],
		]);
	}

}
