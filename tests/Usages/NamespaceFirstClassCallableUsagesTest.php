<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\UsageFactory\NamespaceUsageFactory;

/**
 * @requires PHP >= 8.1.0
 * @extends RuleTestCase<NamespaceFirstClassCallableUsages>
 */
#[RequiresPhp('>= 8.1.0')]
class NamespaceFirstClassCallableUsagesTest extends RuleTestCase
{

	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NamespaceFirstClassCallableUsages(
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
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/Enums.php'], [
			[
				'Enum Enums\Enum is forbidden.',
				66,
			],
			[
				'Enum Enums\BackedEnum is forbidden.',
				67,
			],
			[
				'Class DateTimeImmutable is forbidden.',
				68,
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
