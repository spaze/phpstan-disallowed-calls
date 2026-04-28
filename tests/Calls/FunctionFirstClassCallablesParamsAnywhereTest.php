<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionFirstClassCallables>
 */
class FunctionFirstClassCallablesParamsAnywhereTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionFirstClassCallables(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => 'crc32()',
					'allowParamsAnywhere' => [
						1 => 'a',
					],
				],
				[
					'function' => 'strtolower()',
					'allowExceptParamsAnywhere' => [
						1 => 'a',
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
		$this->analyse([__DIR__ . '/../src/FirstClassCallableParamsAnywhere.php'], [
			[
				'Calling crc32() is forbidden.',
				4,
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
