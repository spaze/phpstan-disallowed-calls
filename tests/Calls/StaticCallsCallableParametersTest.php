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

class StaticCallsCallableParametersTest extends RuleTestCase
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
				],
			],
			[
				[
					'method' => 'Callbacks::call()',
				],
				[
					'method' => 'CallbacksInterface::interfaceCall()',
				],
				[
					'method' => 'CallbacksTrait::traitCall()',
				],
			],
			[
				[
					'method' => 'Callbacks::staticCall()',
				],
				[
					'method' => 'CallbacksInterface::interfaceStaticCall()',
				],
				[
					'method' => 'CallbacksTrait::traitStaticCall()',
				],
			],
		);
		return new StaticCalls(
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
				99,
			],
			[
				'Calling var_dump() is forbidden.',
				100,
			],
			[
				'Calling var_dump() is forbidden.',
				101,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				102,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				103,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				104,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				105,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				106,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				107,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				108,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				109,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				110,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				111,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				112,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				113,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				114,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				115,
			],
			[
				'Calling var_dump() is forbidden.',
				117,
			],
			[
				'Calling var_dump() is forbidden.',
				118,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				119,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				120,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				121,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				122,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				123,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				124,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				125,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				126,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				127,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				128,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				129,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				130,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				131,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				132,
			],
			[
				'Calling CallbacksInterface::interfaceCall() (as CallbacksPlusPlus::interfaceCall()) is forbidden.',
				165,
			],
			[
				'Calling CallbacksInterface::interfaceStaticCall() (as CallbacksPlusPlus::interfaceStaticCall()) is forbidden.',
				166,
			],
			[
				'Calling CallbacksTrait::traitCall() (as CallbacksPlusPlus::traitCall()) is forbidden.',
				167,
			],
			[
				'Calling CallbacksTrait::traitStaticCall() (as CallbacksPlusPlus::traitStaticCall()) is forbidden.',
				168,
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
