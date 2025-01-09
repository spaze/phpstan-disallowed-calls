<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallsRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedMethodRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeResolver;

class NewCallsCallableParametersTest extends RuleTestCase
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
		return new NewCalls(
			$container->getByType(DisallowedCallsRuleErrors::class),
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
				176,
			],
			[
				'Calling var_dump() is forbidden.',
				177,
			],
			[
				'Calling var_dump() is forbidden.',
				178,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				179,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				180,
			],
			[
				'Calling Callbacks::call() is forbidden.',
				181,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				182,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				183,
			],
			[
				'Calling Callbacks::call() (as Callbacks2::call()) is forbidden.',
				184,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				185,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				186,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				187,
			],
			[
				'Calling Callbacks::staticCall() is forbidden.',
				188,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				189,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				190,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				191,
			],
			[
				'Calling Callbacks::staticCall() (as Callbacks2::staticCall()) is forbidden.',
				192,
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
