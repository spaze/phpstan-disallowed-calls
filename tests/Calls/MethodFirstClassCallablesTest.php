<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;

class MethodFirstClassCallablesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new MethodFirstClassCallables(
			$container->getByType(DisallowedMethodRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'method' => 'Waldo\Quux\Blade::run*()',
					'message' => "I've seen tests you people wouldn't believe",
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
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe. [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				// on this line:
				10,
			],
			[
				"Calling Waldo\Quux\Blade::runner() is forbidden, I've seen tests you people wouldn't believe. [Waldo\Quux\Blade::runner() matches Waldo\Quux\Blade::run*()]",
				13,
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
