<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;

/**
 * @extends RuleTestCase<ConstantUsages>
 */
class ConstantUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$normalizer = new Normalizer();
		$container = self::getContainer();
		return new ConstantUsages(
			$container->getByType(DisallowedConstantRuleErrors::class),
			$container->getByType(DisallowedConstantFactory::class),
			[
				[
					'constant' => [
						'FILTER_FLAG_NO_PRIV_RANGE',
						'FILTER_FLAG_NO_RES_RANGE',
					],
					'message' => 'the cake is a lie',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'constant' => '\FILTER_FLAG_NO_PRIV_RANGE',
					'message' => 'the cake is a lie',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'errorTip' => 'Use https://github.com/mlocati/ip-lib instead',
				],
				// test disallowed paths
				[
					'constant' => 'PHP_EOL',
					'allowExceptIn' => [
						__DIR__ . '/../src/disallowed/*.php',
					],
				],
				// test allowed instances
				[
					'constant' => 'FILTER_FLAG_EMAIL_UNICODE',
					'allowInInstanceOf' => [
						'\Constants\Constants',
					],
				],
				[
					'constant' => 'FILTER_FLAG_ENCODE_HIGH',
					'allowExceptInInstanceOf' => [
						'\Constants\Constants',
					],
				],
				// test allowed methods
				[
					'constant' => 'FILTER_FLAG_ALLOW_HEX',
					'allowInMethods' => [
						'\Constants\ChildConstants::leMethod()',
					],
				],
				[
					'constant' => 'FILTER_FLAG_NO_ENCODE_QUOTES',
					'disallowInMethods' => [
						'\Constants\ChildConstants::leMethod()',
					],
				],
				// test allowed attributes
				[
					'constant' => 'FILTER_FLAG_ALLOW_OCTAL',
					'allowInMethodsWithAttributes' => [
						'\Attributes\AttributeClass',
					],
				],
				[
					'constant' => 'FILTER_FLAG_ALLOW_FRACTION',
					'allowExceptInMethodsWithAttributes' => [
						'\Attributes\AttributeColumn2',
					],
				],
				[
					'constant' => 'FILTER_FLAG_ENCODE_AMP',
					'allowInClassWithAttributes' => [
						'\Attributes\AttributeClass',
					],
				],
				[
					'constant' => 'FILTER_FLAG_ENCODE_LOW',
					'allowExceptInClassWithAttributes' => [
						'\Attributes\AttributeColumn2',
					],
				],
				[
					'constant' => 'FILTER_FLAG_IPV4',
					'allowInClassWithMethodAttributes' => [
						'AttributeClass2',
					],
				],
				[
					'constant' => 'FILTER_FLAG_IPV6',
					'allowExceptInClassWithMethodAttributes' => [
						'Attributes\AttributeColumn3',
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
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie.',
				// on this line:
				8,
				'Use https://github.com/mlocati/ip-lib instead',
			],
			[
				'Using FILTER_FLAG_NO_PRIV_RANGE is forbidden, the cake is a lie.',
				9,
				'Use https://github.com/mlocati/ip-lib instead',
			],
			[
				'Using FILTER_FLAG_NO_RES_RANGE is forbidden, the cake is a lie.',
				10,
			],
			[
				'Using PHP_EOL is forbidden.',
				40,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/constantUsages.php'], []);
		$this->analyse([__DIR__ . '/../src/Constants.php'], [
			[
				'Using FILTER_FLAG_ENCODE_HIGH is forbidden.',
				22,
			],
			[
				'Using FILTER_FLAG_NO_ENCODE_QUOTES is forbidden.',
				24,
			],
			[
				'Using FILTER_FLAG_EMAIL_UNICODE is forbidden.',
				47,
			],
			[
				'Using FILTER_FLAG_ALLOW_HEX is forbidden.',
				49,
			],
			[
				'Using FILTER_FLAG_ALLOW_OCTAL is forbidden.',
				51,
			],
			[
				'Using FILTER_FLAG_ALLOW_FRACTION is forbidden.',
				52,
			],
			[
				'Using FILTER_FLAG_ENCODE_AMP is forbidden.',
				53,
			],
			[
				'Using FILTER_FLAG_ENCODE_LOW is forbidden.',
				54,
			],
			[
				'Using FILTER_FLAG_IPV4 is forbidden.',
				55,
			],
			[
				'Using FILTER_FLAG_IPV6 is forbidden.',
				56,
			],
		]);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
