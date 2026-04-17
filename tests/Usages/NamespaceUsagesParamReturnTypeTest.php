<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * @extends RuleTestCase<NamespaceUsages>
 */
class NamespaceUsagesParamReturnTypeTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceUsages(
			$container->getByType(DisallowedNamespaceRuleErrors::class),
			$container->getByType(DisallowedNamespaceFactory::class),
			$container->getByType(NamespaceUsageFactory::class),
			[
				[
					'class' => 'Waldo\Quux\Blade',
					'message' => 'do androids dream of electric sheep?',
					'disallowInParamTypes' => true,
				],
			]
		);
	}


	public function testRule(): void
	{
		// With disallowInParamTypes: true, only param type hint positions are flagged.
		// Property types, return types, use statements, new expressions are all allowed.
		$this->analyse([__DIR__ . '/../src/TypesEverywhere.php'], [
			[
				// expect this error message:
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				// on this line (constructor promoted param):
				19,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				20,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				21,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				22,
			],
			[
				// method param type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				28,
			],
			[
				// nullable method param type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				34,
			],
			[
				// union method param type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				40,
			],
			[
				// intersection method param type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				46,
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
