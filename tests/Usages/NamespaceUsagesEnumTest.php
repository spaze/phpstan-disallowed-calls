<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\ErrorIdentifiers;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * @extends RuleTestCase<NamespaceUsages>
 */
class NamespaceUsagesEnumTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceUsages(
			$container->getByType(DisallowedNamespaceRuleErrors::class),
			$container->getByType(DisallowedNamespaceFactory::class),
			$container->getByType(NamespaceUsageFactory::class),
			[
				[
					'class' => 'Enums\Enum',
				],
				[
					'namespace' => 'Enums\BackedEnum',
				],
				[
					'class' => 'DateTimeImmutable',
				],
				[
					'class' => 'Enums\EnumInterface',
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/Enums.php'], [
			[
				'Enum Enums\Enum is forbidden.',
				16,
			],
			[
				'Enum Enums\Enum is forbidden.',
				17,
			],
			[
				'Enum Enums\Enum is forbidden.',
				18,
			],
			[
				'Enum Enums\Enum is forbidden.',
				19,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				31,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				32,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				33,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				34,
			],
			[
				'Enum Enums\Enum is forbidden.',
				37,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				38,
			],
			[
				'Enum Enums\Enum is forbidden.',
				39,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				39,
			],
			[
				'Enum Enums\Enum is forbidden.',
				40,
			],
			[
				'Enum Enums\Enum is forbidden.',
				46,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				47,
			],
			[
				'Enum Enums\Enum is forbidden.',
				52,
			],
			[
				'Interface Enums\EnumInterface is forbidden.',
				59,
			],
		]);
	}


	public function testIdentifiers(): void
	{
		$errors = [];
		foreach ($this->gatherAnalyserErrors([__DIR__ . '/../src/Enums.php']) as $error) {
			$errors[] = [$error->getLine(), $error->getMessage(), $error->getIdentifier()];
		}
		sort($errors);
		$this->assertSame([
			[16, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[17, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[18, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[19, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[31, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[32, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[33, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[34, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[37, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[38, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[39, 'Class DateTimeImmutable is forbidden.', ErrorIdentifiers::DISALLOWED_CLASS],
			[39, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[40, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[46, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[47, 'Enum Enums\BackedEnum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[52, 'Enum Enums\Enum is forbidden.', ErrorIdentifiers::DISALLOWED_ENUM],
			[59, 'Interface Enums\EnumInterface is forbidden.', ErrorIdentifiers::DISALLOWED_INTERFACE],
		], $errors);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
