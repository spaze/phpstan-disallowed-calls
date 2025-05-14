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

/**
 * @extends RuleTestCase<MethodCalls>
 */
class MethodCallsCallableParametersTest extends RuleTestCase
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
		return new MethodCalls(
			$container->getByType(DisallowedMethodRuleErrors::class),
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
				63,
			],
			[
				'Calling var_dump() is forbidden.',
				64,
			],
			[
				'Calling var_dump() is forbidden.',
				65,
			],
			[
				'Calling var_dump() is forbidden.',
				66,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				69,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				70,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				71,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				72,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				73,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				74,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				75,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				76,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				77,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				78,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				79,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				80,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				81,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				82,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				84,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				85,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				86,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				87,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				88,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				89,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				90,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				91,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				92,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				93,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				94,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				95,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				96,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				97,
			],
			[
				'Calling CallbacksInterface::interfaceCall() (as CallbacksPlusPlus::interfaceCall()) is forbidden.',
				160,
			],
			[
				'Calling CallbacksInterface::interfaceStaticCall() (as CallbacksPlusPlus::interfaceStaticCall()) is forbidden.',
				161,
			],
			[
				'Calling CallbacksTrait::traitCall() (as CallbacksPlusPlus::traitCall()) is forbidden.',
				162,
			],
			[
				'Calling CallbacksTrait::traitStaticCall() (as CallbacksPlusPlus::traitStaticCall()) is forbidden.',
				163,
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
