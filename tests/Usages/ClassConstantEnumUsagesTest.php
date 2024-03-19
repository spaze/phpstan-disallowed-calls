<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use Enums\BackedEnum;
use Enums\Enum;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstantFactory;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedConstantRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

/**
 * @requires PHP >= 8.1
 */
#[RequiresPhp('>= 8.1')]
class ClassConstantEnumUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new ClassConstantUsages(
			$container->getByType(DisallowedConstantRuleErrors::class),
			$container->getByType(DisallowedConstantFactory::class),
			$container->getByType(TypeResolver::class),
			$container->getByType(Formatter::class),
			[
				[
					'enum' => Enum::class,
					'constant' => 'ENUM_CONST',
				],
				[
					'enum' => Enum::class,
					'case' => 'Foo',
				],
				[
					'class' => Enum::class,
					'constant' => 'Bar',
				],
				[
					'enum' => BackedEnum::class,
					'constant' => 'ENUM_CONST',
				],
				[
					'enum' => BackedEnum::class,
					'case' => 'Waldo',
				],
				[
					'class' => BackedEnum::class,
					'constant' => 'Quux',
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/Enums.php'], [
			[
				// expect this error message:
				'Using Enums\Enum::ENUM_CONST is forbidden.',
				// on this line:
				16,
			],
			[
				'Using Enums\Enum::Foo is forbidden.',
				17,
			],
			[
				'Using Enums\Enum::Bar is forbidden.',
				18,
			],
			[
				'Using Enums\BackedEnum::ENUM_CONST is forbidden.',
				31,
			],
			[
				'Using Enums\BackedEnum::Waldo is forbidden.',
				32,
			],
			[
				'Using Enums\BackedEnum::Quux is forbidden.',
				33,
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
