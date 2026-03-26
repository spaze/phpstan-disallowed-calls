<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Bugs;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\FunctionCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class Bug383RuleWithDefinedInSkipsBuiltinFunctionsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'function' => '*',
					'definedIn' => __DIR__ . '/../src/Fun*.php',
				],
			],
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/bugs/Bug383RuleWithDefinedInSkipsBuiltinFunctions.php'], [
			[
				'Calling __() is forbidden. [__() matches *()]',
				12,
			],
			[
				'Calling MyNamespace\__() is forbidden. [MyNamespace\__() matches *()]',
				13,
			],
			[
				'Calling Foo\Bar\Waldo\foo() is forbidden. [Foo\Bar\Waldo\foo() matches *()]',
				14,
			],
			[
				'Calling Foo\Bar\Waldo\config() is forbidden. [Foo\Bar\Waldo\config() matches *()]',
				15,
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
