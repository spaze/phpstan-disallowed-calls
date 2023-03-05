<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class ClassConstantUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		return new ClassConstantUsages(
			new DisallowedConstantRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedConstantFactory(),
			new TypeResolver(),
			[
				[
					'class' => '\Inheritance\Base',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'class' => 'Inheritance\Base',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'class' => '\Inheritance\Sub',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'class' => 'Waldo\Quux\Blade',
					'constant' => [
						'RUNNER',
						'WESLEY',
					],
					'message' => 'not a replicant',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				// test param overwriting
				[
					'class' => 'Waldo\Quux\Blade',
					'constant' => 'DECKARD',
					'message' => 'maybe a replicant',
				],
				[
					'class' => 'Waldo\Quux\Blade',
					'constant' => 'DECKARD',
					'message' => 'maybe a replicant',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'class' => 'PhpOption\Option',
					'constant' => 'NAME',
					'message' => 'no PhpOption',
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
		$this->analyse([__DIR__ . '/../src/disallowed/constantUsages.php'], [
			[
				// expect this error message:
				'Using Inheritance\Base::BELONG (as Inheritance\Sub::BELONG) is forbidden, belong to us',
				// on this line:
				11,
			],
			[
				'Using Inheritance\Base::BELONG is forbidden, belong to us',
				12,
			],
			[
				'Using Inheritance\Base::BELONG is forbidden, belong to us',
				13,
			],
			[
				'Using Waldo\Quux\Blade::RUNNER is forbidden, not a replicant',
				14,
			],
			[
				'Using Waldo\Quux\Blade::RUNNER is forbidden, not a replicant',
				15,
			],
			[
				'Using Waldo\Quux\Blade::DECKARD is forbidden, maybe a replicant',
				19,
			],
			[
				'Using Waldo\Quux\Blade::DECKARD is forbidden, maybe a replicant',
				22,
			],
			[
				'Using Waldo\Quux\Blade::WESLEY is forbidden, not a replicant',
				23,
			],
			[
				'Using PhpOption\Option::NAME is forbidden, no PhpOption',
				37,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/constantUsages.php'], []);
	}

}
