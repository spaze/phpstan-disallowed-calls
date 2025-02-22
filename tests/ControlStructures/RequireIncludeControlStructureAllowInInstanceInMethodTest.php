<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructureFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedControlStructureRuleErrors;

class RequireIncludeControlStructureAllowInInstanceInMethodTest extends RuleTestCase
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
				// test allowed instances
				[
					'controlStructure' => 'require',
					'allowInInstanceOf' => [
						'\ControlStructures\ControlStructures',
					],
				],
				[
					'controlStructure' => 'require_once',
					'disallowInInstanceOf' => [
						'\ControlStructures\ControlStructures2',
					],
				],
				// test allowed methods
				[
					'controlStructure' => 'include',
					'allowInMethods' => [
						'\ControlStructures\ChildControlStructures::leMethod()',
					],
				],
				[
					'controlStructure' => 'include_once',
					'disallowInMethods' => [
						'\ControlStructures\ChildControlStructures::leMethod()',
					],
				],
			]),
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/ControlStructuresRequireInclude.php'], [
			[
				'Using the include_once control structure is forbidden.',
				18,
			],
			[
				'Using the require control structure is forbidden.',
				28,
			],
			[
				'Using the require_once control structure is forbidden.',
				29,
			],
			[
				'Using the include control structure is forbidden.',
				30,
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
