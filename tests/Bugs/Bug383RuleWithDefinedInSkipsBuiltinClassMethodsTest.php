<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Bugs;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\MethodCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;

/**
 * @extends RuleTestCase<MethodCalls>
 */
class Bug383RuleWithDefinedInSkipsBuiltinClassMethodsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new MethodCalls(
			$container->getByType(DisallowedMethodRuleErrors::class),
			$container->getByType(DisallowedCallableParameterRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			[
				[
					'method' => '*',
					'definedIn' => [
						__DIR__ . '/../src/Blade*',
						'phar://*', // The built-in classes are defined in PHPStan phar
					],
				],
			],
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/bugs/Bug383RuleWithDefinedInSkipsBuiltinClasses.php'], [
			[
				'Calling Waldo\Quux\Blade::runner() is forbidden. [Waldo\Quux\Blade::runner() matches *()]',
				19,
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
