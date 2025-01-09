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

class FunctionCallsCallableParametersTest extends RuleTestCase
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
					'function' => 'var_dump()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			],
			[
				[
					'method' => 'Callbacks::call()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'method' => 'CallbacksInterface::interfaceCall()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'method' => 'CallbacksTrait::traitCall()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			],
			[
				[
					'method' => 'Callbacks::staticCall()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'method' => 'CallbacksInterface::interfaceStaticCall()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
				[
					'method' => 'CallbacksTrait::traitStaticCall()',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			],
		);
		return new FunctionCalls(
			$container->getByType(DisallowedFunctionRuleErrors::class),
			$disallowedCallableParameterRuleErrors,
			$container->getByType(DisallowedCallFactory::class),
			[],
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/callableParameters.php'], [
			[
				// expect this error message:
				'Calling var_dump() is forbidden.',
				// on this line:
				23,
			],
			[
				'Calling var_dump() is forbidden.',
				24,
			],
			[
				'Calling var_dump() is forbidden.',
				25,
			],
			[
				'Calling var_dump() is forbidden.',
				26,
			],
			[
				'Calling var_dump() is forbidden.',
				28,
			],
			[
				'Calling var_dump() is forbidden.',
				29,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				40,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				41,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				42,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				43,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				45,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				46,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				47,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				49,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				50,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				51,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				52,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				54,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				55,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				56,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				57,
			],
			[
				'Calling CallbacksInterface::interfaceCall() (as CallbacksPlusPlus::interfaceCall()) is forbidden.',
				155,
			],
			[
				'Calling CallbacksInterface::interfaceStaticCall() (as CallbacksPlusPlus::interfaceStaticCall()) is forbidden.',
				156,
			],
			[
				'Calling CallbacksTrait::traitCall() (as CallbacksPlusPlus::traitCall()) is forbidden.',
				157,
			],
			[
				'Calling CallbacksTrait::traitStaticCall() (as CallbacksPlusPlus::traitStaticCall()) is forbidden.',
				158,
			],
		]);
		// Based on the configuration above, no errors in this file:
		$this->analyse([__DIR__ . '/../src/disallowed-allow/callableParameters.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
