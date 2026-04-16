<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedPropertyFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedPropertyRuleErrors;

/**
 * @extends RuleTestCase<InstancePropertyUsages>
 */
class EnumPropertyUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new InstancePropertyUsages(
			$container->getByType(DisallowedPropertyFactory::class),
			$container->getByType(DisallowedPropertyRuleErrors::class),
			[
				[
					'property' => 'Enums\BackedEnum::value',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			]
		);
	}


	/**
	 * @requires PHP >= 8.1.0
	 */
	#[RequiresPhp('>= 8.1.0')]
	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/enumPropertyUsages.php'], [
			[
				// expect this error message:
				'Using Enums\BackedEnum::$value is forbidden.',
				// on this line:
				6,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/enumPropertyUsages.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
