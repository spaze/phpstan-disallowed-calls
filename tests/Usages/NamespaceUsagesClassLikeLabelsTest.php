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
class NamespaceUsagesClassLikeLabelsTest extends RuleTestCase
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
					'class' => 'ClassLikeLabels\SomeInterface',
				],
				[
					'class' => 'ClassLikeLabels\SomeTrait',
				],
				[
					'class' => 'ClassLikeLabels\SomeClass',
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/ClassLikeLabels.php'], [
			[
				'Interface ClassLikeLabels\SomeInterface is forbidden.',
				22,
			],
			[
				'Class ClassLikeLabels\SomeClass is forbidden.',
				22,
			],
			[
				'Interface ClassLikeLabels\SomeInterface is forbidden.',
				24,
			],
			[
				'Trait ClassLikeLabels\SomeTrait is forbidden.',
				25,
			],
			[
				'Class ClassLikeLabels\SomeClass is forbidden.',
				26,
			],
		]);
	}


	public function testIdentifiers(): void
	{
		$errors = [];
		foreach ($this->gatherAnalyserErrors([__DIR__ . '/../src/ClassLikeLabels.php']) as $error) {
			$errors[] = [$error->getLine(), $error->getMessage(), $error->getIdentifier()];
		}
		sort($errors);
		$this->assertSame([
			[22, 'Class ClassLikeLabels\SomeClass is forbidden.', ErrorIdentifiers::DISALLOWED_CLASS],
			[22, 'Interface ClassLikeLabels\SomeInterface is forbidden.', ErrorIdentifiers::DISALLOWED_INTERFACE],
			[24, 'Interface ClassLikeLabels\SomeInterface is forbidden.', ErrorIdentifiers::DISALLOWED_INTERFACE],
			[25, 'Trait ClassLikeLabels\SomeTrait is forbidden.', ErrorIdentifiers::DISALLOWED_TRAIT],
			[26, 'Class ClassLikeLabels\SomeClass is forbidden.', ErrorIdentifiers::DISALLOWED_CLASS],
		], $errors);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
