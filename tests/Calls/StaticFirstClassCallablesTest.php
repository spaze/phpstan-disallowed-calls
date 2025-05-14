<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;

/**
 * @extends RuleTestCase<StaticFirstClassCallables>
 */
class StaticFirstClassCallablesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new StaticFirstClassCallables(
			$container->getByType(DisallowedMethodRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'method' => 'Fiction\Pulp\Royale::withoutCheese',
					'message' => 'a Quarter Pounder without Cheese!',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			]
		);
	}


	/**
	 * @requires PHP >= 8.1
	 */
	#[RequiresPhp('>= 8.1')]
	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/firstClassCallable.php'], [
			[
				// expect this error message:
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese!',
				// on this line:
				16,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese!',
				19,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese!',
				23,
			],
			[
				'Calling Fiction\Pulp\Royale::withoutCheese() is forbidden, a Quarter Pounder without Cheese!',
				26,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/firstClassCallable.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
