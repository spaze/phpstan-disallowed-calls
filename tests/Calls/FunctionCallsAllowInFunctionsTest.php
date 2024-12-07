<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class FunctionCallsAllowInFunctionsTest extends RuleTestCase
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
			$container->getByType(Normalizer::class),
			$container->getByType(TypeResolver::class),
			[
				[
					'function' => 'md*()',
					'allowInFunctions' => [
						'\\Foo\\Bar\\Waldo\\qu*x()',
					],
				],
				[
					'function' => 'sha*()',
					'allowExceptInFunctions' => [
						'\\Foo\\Bar\\Waldo\\fred()',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/Functions.php'], [
			[
				// expect this error message:
				'Calling sha1() is forbidden. [sha1() matches sha*()]',
				// on this line:
				14,
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
