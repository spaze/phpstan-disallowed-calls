<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespaceFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedNamespaceRuleErrors;

/**
 * @requires PHP >= 8.0
 */
#[RequiresPhp('>= 8.0')]
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
					'namespace' => 'PrivateModule\*',
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
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/namespaceUsagesExcludeAttribute.php'], [
			[
				'Namespace PrivateModule\PrivateClass is forbidden, no private modules. [PrivateModule\PrivateClass matches PrivateModule\*]',
				6,
			],
			[
				'Class PrivateModule\PrivateClass is forbidden, no private modules. [PrivateModule\PrivateClass matches PrivateModule\*]',
				18,
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
