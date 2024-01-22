<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

/**
 * @requires PHP >= 8.1
 */
class NamespaceUsagesTypesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceUsages(
			$container->getByType(DisallowedNamespaceRuleErrors::class),
			$container->getByType(DisallowedNamespaceFactory::class),
			$container->getByType(Normalizer::class),
			[
				[
					'class' => 'Waldo\Quux\Blade',
					'message' => 'do androids dream of electric sheep?',
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/TypesEverywhere.php'], [
			[
				// expect this error message:
				'Namespace Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				// on this line:
				7,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				12,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				13,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				14,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				15,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
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
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				28,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				29,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				34,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				35,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				40,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				41,
			],
			[
				'Class Waldo\Quux\Blade is forbidden, do androids dream of electric sheep?',
				46,
			],
			[
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
