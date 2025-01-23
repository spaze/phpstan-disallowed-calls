<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class Bug288DocblockTemplateCallableParamTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		$disallowedCallableParameterRuleErrors = new DisallowedCallableParameterRuleErrors(
			$container->getByType(TypeResolver::class),
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$container->getByType(DisallowedMethodRuleErrors::class),
			$container->getByType(DisallowedCallFactory::class),
			$container->getByType(ReflectionProvider::class),
			[
				[
					'function' => 'disallowedFunction()',
				],
			],
			[],
			[],
		);
		return new MethodCalls(
			$container->getByType(DisallowedMethodRuleErrors::class),
			$disallowedCallableParameterRuleErrors,
			$container->getByType(DisallowedCallFactory::class),
			[],
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/bugs/Bug288DocblockTemplateCallableParam.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
