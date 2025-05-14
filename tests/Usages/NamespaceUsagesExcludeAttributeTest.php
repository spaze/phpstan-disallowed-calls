<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

/**
 * @extends RuleTestCase<NamespaceUsages>
 */
class NamespaceUsagesExcludeAttributeTest extends RuleTestCase
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
					'namespace' => 'NoBigDeal\*',
					'message' => 'no private modules',
					'excludeWithAttribute' => [
						'\Attributes\*Class',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/disallowed/namespaceUsagesExcludeAttribute.php'], [
			[
				'Class NoBigDeal\PrivateClass is forbidden, no private modules. [NoBigDeal\PrivateClass matches NoBigDeal\*]',
				17,
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
