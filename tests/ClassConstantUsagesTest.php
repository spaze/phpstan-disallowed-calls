<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class ClassConstantUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ClassConstantUsages(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'constant' => '\Inheritance\Base::BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'constant' => 'Inheritance\Base::BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'constant' => '\Inheritance\Sub::BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'constant' => 'Waldo\Quux\Blade::RUNNER',
					'message' => 'not a replicant',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				// test param overwriting
				[
					'constant' => 'Waldo\Quux\Blade::DECKARD',
					'message' => 'maybe a replicant',
				],
				[
					'constant' => 'Waldo\Quux\Blade::DECKARD',
					'message' => 'maybe a replicant',
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
		$this->analyse([__DIR__ . '/src/disallowed/constantUsages.php'], [
			[
				// expect this error message:
				'Using Inheritance\Base::BELONG (as Inheritance\Sub::BELONG) is forbidden, belong to us',
				// on this line:
				10,
			],
			[
				'Using Inheritance\Base::BELONG is forbidden, belong to us',
				11,
			],
			[
				'Using Inheritance\Base::BELONG is forbidden, belong to us',
				12,
			],
			[
				'Using Waldo\Quux\Blade::RUNNER is forbidden, not a replicant',
				13,
			],
			[
				'Using Waldo\Quux\Blade::RUNNER is forbidden, not a replicant',
				14,
			],
			[
				'Using Waldo\Quux\Blade::DECKARD is forbidden, maybe a replicant',
				18,
			],
			[
				'Using Waldo\Quux\Blade::DECKARD is forbidden, maybe a replicant',
				21,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/constantUsages.php'], []);
	}

}
