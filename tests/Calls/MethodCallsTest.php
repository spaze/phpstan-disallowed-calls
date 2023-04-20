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
use Spaze\PHPStan\Rules\Disallowed\Formatter\MethodFormatter;
use Spaze\PHPStan\Rules\Disallowed\IdentifierFormatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class MethodCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		return new MethodCalls(
			new DisallowedMethodRuleErrors(
				new DisallowedRuleErrors(new Allowed(new MethodFormatter(), new AllowedPath(new FileHelper(__DIR__)))),
				new TypeResolver(),
				new IdentifierFormatter(),
				new MethodFormatter()
			),
			new DisallowedCallFactory(new IdentifierFormatter()),
			[
				[
					'method' => 'Waldo\Quux\Blade::run*()',
					'message' => "I've seen tests you people wouldn't believe",
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowParamsInAllowed' => [
						1 => 42,
						2 => true,
						3 => '909',
					],
				],
				[
					'method' => 'Waldo\Quux\Blade::movie()',
					'message' => 'was good',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Waldo\Quux\Blade::sequel()',
					'message' => 'too',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Waldo\Quux\Blade::Trinity()',
					'message' => 'holy trinity',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Inheritance\Base::x*()',
					'message' => 'Base::x*() methods are dangerous',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\TestTrait::*',
					'message' => 'all TestTrait methods are dangerous',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'Traits\AnotherTestClass::zzTop()',
					'message' => 'method AnotherTestClass::zzTop() is dangerous',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\None::getIterator()',
					'message' => 'no PhpOption',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'method' => 'PhpOption\Some::getIterator()',
					'message' => 'no PhpOption',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test disallowed param values
				[
					'function' => 'DateTime::format()',
					'message' => 'why too kay',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'allowExceptParams' => [
						1 => 'y',
					],
					'errorTip' => '2038 is approaching fast',
				],
				// test disallowed paths
				[
					'method' => 'Waldo\Quux\Blade::andSorcery()',
					'message' => 'use magic',
					'allowExceptIn' => [
						'../src/disallowed/*.php',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/methodCalls.php'], [
			[
				// expect this error message:
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				// on this line:
				10,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				11,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				14,
			],
			[
				'Calling Inheritance\Base::x() (as Inheritance\Sub::x()) is forbidden, Base::x*() methods are dangerous [Inheritance\Base::x() matches Inheritance\Base::x*()]',
				22,
			],
			[
				'Calling Traits\TestTrait::x() (as Traits\TestClass::x()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::x() matches Traits\TestTrait::*()]',
				26,
			],
			[
				'Calling Traits\TestTrait::y() (as Traits\AnotherTestClass::y()) is forbidden, all TestTrait methods are dangerous [Traits\TestTrait::y() matches Traits\TestTrait::*()]',
				28,
			],
			[
				'Calling Traits\AnotherTestClass::zzTop() is forbidden, method AnotherTestClass::zzTop() is dangerous',
				29,
			],
			[
				'Calling PhpOption\None::getIterator() is forbidden, no PhpOption',
				46,
			],
			[
				'Calling PhpOption\Some::getIterator() is forbidden, no PhpOption',
				52,
			],
			[
				'Calling DateTime::format() is forbidden, why too kay',
				55,
				'2038 is approaching fast',
			],
			[
				'Calling Waldo\Quux\Blade::movie() is forbidden, was good',
				60,
			],
			[
				'Calling Waldo\Quux\Blade::movie() is forbidden, was good',
				61,
			],
			[
				'Calling Waldo\Quux\Blade::Sequel() is forbidden, too [Waldo\Quux\Blade::Sequel() matches Waldo\Quux\Blade::sequel()]',
				62,
			],
			[
				'Calling Waldo\Quux\Blade::Sequel() is forbidden, too [Waldo\Quux\Blade::Sequel() matches Waldo\Quux\Blade::sequel()]',
				63,
			],
			[
				'Calling Waldo\Quux\Blade::trinity() is forbidden, holy trinity [Waldo\Quux\Blade::trinity() matches Waldo\Quux\Blade::Trinity()]',
				64,
			],
			[
				'Calling Waldo\Quux\Blade::trinity() is forbidden, holy trinity [Waldo\Quux\Blade::trinity() matches Waldo\Quux\Blade::Trinity()]',
				65,
			],
			[
				'Calling Waldo\Quux\Blade::andSorcery() is forbidden, use magic',
				68,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/methodCalls.php'], [
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				10,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				11,
			],
		]);
	}

}
