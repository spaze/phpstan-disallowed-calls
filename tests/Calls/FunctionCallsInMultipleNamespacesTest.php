<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

class FunctionCallsInMultipleNamespacesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedCallsRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			$this->createReflectionProvider(),
			[
				[
					'function' => '__()',
					'message' => 'use MyNamespace\__ instead',
				],
				[
					'function' => 'MyNamespace\__()',
					'message' => 'ha ha ha nope',
				],
				[
					'function' => 'printf()',
				],
			],
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/FunctionInMultipleNamespaces.php'], [
			[
				// expect this error message:
				'Calling __() (as alias()) is forbidden, use MyNamespace\__ instead.',
				// on this line:
				20,
			],
			[
				'Calling MyNamespace\__() (as __()) is forbidden, ha ha ha nope.',
				26,
			],
			[
				'Calling printf() is forbidden.',
				30,
			],
			[
				'Calling printf() is forbidden.',
				31,
			],
			[
				'Calling MyNamespace\__() (as alias()) is forbidden, ha ha ha nope.',
				39,
			],
			[
				'Calling printf() is forbidden.',
				40,
			],
			[
				'Calling printf() is forbidden.',
				41,
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
