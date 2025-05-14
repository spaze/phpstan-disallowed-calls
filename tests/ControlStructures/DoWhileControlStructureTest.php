<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructureFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;

/**
 * @extends RuleTestCase<DoWhileControlStructure>
 */
class DoWhileControlStructureTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new DoWhileControlStructure(
			$container->getByType(DisallowedControlStructureRuleErrors::class),
			$container->getByType(DisallowedControlStructureFactory::class)->getDisallowedControlStructures([
				[
					'controlStructure' => 'do-while',
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
				'Using the do-while control structure is forbidden.',
				// on this line:
				46,
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
