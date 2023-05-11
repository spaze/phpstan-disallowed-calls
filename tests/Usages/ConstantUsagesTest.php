<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\File\FileHelper;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;

class ConstantUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		return new ConstantUsages(
			new DisallowedConstantRuleErrors(new AllowedPath(new FileHelper(__DIR__))),
			new DisallowedConstantFactory(new Normalizer()),
			[
				[
					'constant' => [
						'FILTER_FLAG_NO_PRIV_RANGE',
						'FILTER_FLAG_NO_RES_RANGE',
					],
					'message' => 'the cake is a lie',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
				],
				[
					'constant' => '\FILTER_FLAG_NO_PRIV_RANGE',
					'message' => 'the cake is a lie',
					'allowIn' => [
						'../src/disallowed-allow/*.php',
						'../src/*-allow/*.*',
					],
					'errorTip' => 'Use https://github.com/mlocati/ip-lib instead',
				],
				// test disallowed paths
				[
					'constant' => 'PHP_EOL',
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
		$this->analyse([__DIR__ . '/../src/disallowed/constantUsages.php'], [
			[
				// expect this error message:
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie',
				// on this line:
				8,
				'Use https://github.com/mlocati/ip-lib instead',
			],
			[
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie',
				9,
				'Use https://github.com/mlocati/ip-lib instead',
			],
			[
				'Using FILTER_FLAG_NO_RES_RANGE is forbidden, the cake is a lie',
				10,
			],
			[
				'Using PHP_EOL is forbidden, because reasons',
				40,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/constantUsages.php'], []);
	}

}
