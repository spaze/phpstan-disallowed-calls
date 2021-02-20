<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class ConstantUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		return new ConstantUsages(
			new DisallowedHelper(new FileHelper(__DIR__)),
			[
				[
					'constant' => 'FILTER_FLAG_NO_PRIV_RANGE',
					'message' => 'the cake is a lie',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'constant' => '\FILTER_FLAG_NO_PRIV_RANGE',
					'message' => 'the cake is a lie',
					'allowIn' => [
						'src/disallowed-allowed/*.php',
						'src/*-allow/*.*',
					],
				],
				[
					'constant' => 'FILTER_FLAG_*_FRACTION',
					'message' => 'the cake is a lie',
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
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie',
				// on this line:
				8,
			],
			[
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie',
				9,
			],
			[
				'Using FILTER_FLAG_*_FRACTION (as FILTER_FLAG_ALLOW_FRACTION) is forbidden, the cake is a lie',
				47,
			],
		]);
		$this->analyse([__DIR__ . '/src/disallowed-allow/constantUsages.php'], []);
	}

}
