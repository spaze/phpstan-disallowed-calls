<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructureFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;

/**
 * @extends RuleTestCase<RequireIncludeControlStructure>
 */
class RequireIncludeControlStructureTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new RequireIncludeControlStructure(
			$container->getByType(DisallowedControlStructureRuleErrors::class),
			$container->getByType(DisallowedControlStructureFactory::class)->getDisallowedControlStructures([
				[
					'controlStructure' => [
						'require',
						'include',
						'require_once',
						'include_once',
					],
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
				'Using the require control structure is forbidden.',
				// on this line:
				120,
			],
			[
				'Using the include control structure is forbidden.',
				121,
			],
			[
				'Using the require_once control structure is forbidden.',
				122,
			],
			[
				'Using the include_once control structure is forbidden.',
				123,
			],
			[
				'Using the require control structure is forbidden.',
				124,
			],
			[
				'Using the include control structure is forbidden.',
				125,
			],
			[
				'Using the require_once control structure is forbidden.',
				126,
			],
			[
				'Using the include_once control structure is forbidden.',
				127,
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
