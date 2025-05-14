<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructureFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;

/**
 * @extends RuleTestCase<BreakControlStructure>
 */
class BreakControlStructureTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new BreakControlStructure(
			$container->getByType(DisallowedControlStructureRuleErrors::class),
			$container->getByType(DisallowedControlStructureFactory::class)->getDisallowedControlStructures([
				[
					'controlStructure' => 'break',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
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
				'Using the break control structure is forbidden.',
				// on this line:
				32,
			],
			[
				'Using the break control structure is forbidden.',
				42,
			],
			[
				'Using the break control structure is forbidden.',
				52,
			],
			[
				'Using the break control structure is forbidden.',
				62,
			],
			[
				'Using the break control structure is forbidden.',
				72,
			],
			[
				'Using the break control structure is forbidden.',
				82,
			],
			[
				'Using the break control structure is forbidden.',
				92,
			],
			[
				'Using the break control structure is forbidden.',
				99,
			],
			[
				'Using the break control structure is forbidden.',
				101,
			],
			[
				'Using the break control structure is forbidden.',
				107,
			],
			[
				'Using the break control structure is forbidden.',
				109,
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
