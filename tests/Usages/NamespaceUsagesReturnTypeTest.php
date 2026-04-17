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
class NamespaceUsagesReturnTypeTest extends RuleTestCase
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
					'disallowInReturnType' => true,
				],
			]
		);
	}


	public function testRule(): void
	{
		// With disallowInReturnType: true, only return type hint positions are flagged.
		// Property types, param types, use statements, new expressions are all allowed.
		$this->analyse([__DIR__ . '/../src/TypesEverywhere.php'], [
			[
				// expect this error message:
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				// on this line (method return type):
				29,
			],
			[
				// nullable return type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				35,
			],
			[
				// union return type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				41,
			],
			[
				// intersection return type
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				47,
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
