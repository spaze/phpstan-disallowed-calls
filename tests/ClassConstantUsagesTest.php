<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use DateTime;
use DateTimeInterface;
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
					'class' => '\Inheritance\Base',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => 'Inheritance\Base',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => '\Inheritance\Sub',
					'constant' => 'BELONG',
					'message' => 'belong to us',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => 'Waldo\Quux\Blade',
					'constant' => 'RUNNER',
					'message' => 'not a replicant',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
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
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => 'PhpOption\Option',
					'constant' => 'NAME',
					'message' => 'no PhpOption',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => 'DateTime*',
					'constant' => 'ISO8601',
					'message' => 'use DateTimeInterface::ATOM instead',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'class' => 'DateTimeInterface',
					'constant' => 'RFC*',
					'message' => 'no RFC',
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
			[
				'Using PhpOption\Option::NAME is forbidden, no PhpOption',
				35,
			],
			[
				'Using DateTime*::ISO8601 (as DateTime::ISO8601) is forbidden, use DateTimeInterface::ATOM instead',
				38,
			],
			[
				'Using DateTime*::ISO8601 (as DateTimeImmutable::ISO8601) is forbidden, use DateTimeInterface::ATOM instead',
				39,
			],
			[
				'Using DateTime*::ISO8601 (as DateTimeInterface::ISO8601) is forbidden, use DateTimeInterface::ATOM instead',
				40,
			],
			[
				'Using DateTimeInterface::RFC* (as DateTimeInterface::RFC1123) is forbidden, no RFC',
				43,
			],
			[
				'Using DateTimeInterface::RFC* (as DateTimeInterface::RFC3339) is forbidden, no RFC',
				44,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/constantUsages.php'], []);
	}

}
