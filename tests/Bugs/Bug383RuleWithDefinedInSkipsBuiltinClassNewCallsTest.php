<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Bugs;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\Calls\NewCalls;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;

/**
 * @extends RuleTestCase<NewCalls>
 */
class Bug383RuleWithDefinedInSkipsBuiltinClassNewCallsTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new NewCalls(
			$container->getByType(DisallowedCallsRuleErrors::class),
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
				'Calling Waldo\Quux\Blade::__construct() is forbidden. [Waldo\Quux\Blade::__construct() matches *()]',
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
