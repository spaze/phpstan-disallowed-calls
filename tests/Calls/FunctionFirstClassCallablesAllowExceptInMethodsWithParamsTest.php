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
class FunctionFirstClassCallablesAllowExceptInMethodsWithParamsTest extends RuleTestCase
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
					'allowExceptInMethods' => [
						'Fiction\Pulp\RoyaleExceptFirstClassCallable::methodA()',
					],
					'allowParamsInAllowed' => [
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
		$this->analyse([__DIR__ . '/../src/RoyaleExceptFirstClassCallable.php'], [
			[
				'Calling crc32() is forbidden.',
				11,
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
