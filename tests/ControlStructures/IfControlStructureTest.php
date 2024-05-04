<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructureFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;

class IfControlStructureTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new IfControlStructure(
			$container->getByType(DisallowedControlStructureRuleErrors::class),
			$container->getByType(DisallowedControlStructureFactory::class)->getDisallowedControlStructures([
				[
					'controlStructure' => 'if',
					'message' => 'what if?',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'errorTip' => 'So long and thanks for all the ifs',
				],
			])
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/controlStructures.php'], [
			[
				// expect this error message:
				'Using the if control structure is forbidden, what if?',
				// on this line:
				8,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				12, // `else if` is parsed as `else` followed by `if`
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				18,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				28,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				31,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				38,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				41,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				48,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				51,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				58,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				61,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				68,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				71,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				78,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				81,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				88,
				'So long and thanks for all the ifs',
			],
			[
				'Using the if control structure is forbidden, what if?',
				91,
				'So long and thanks for all the ifs',
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/controlStructures.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
