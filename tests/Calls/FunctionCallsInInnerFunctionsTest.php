<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @extends RuleTestCase<FunctionCalls>
 */
class FunctionCallsInInnerFunctionsTest extends RuleTestCase
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
					'function' => 'md5()',
					'allowInFunctionsWithAttributes' => [
						'InnerFunctions\AllowMd5',
					],
				],
				[
					'function' => 'sha1()',
					'allowExceptInFunctionsWithAttributes' => [
						'InnerFunctions\DisallowSha1',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		// Inner named functions are not supported by PHPStan and are treated as having no attributes
		$this->analyse([__DIR__ . '/../src/InnerFunctions.php'], [
			[
				'Calling md5() is forbidden.',
				13,
			],
			[
				'Calling md5() is forbidden.',
				26,
			],
			[
				'Calling sha1() is forbidden.',
				36,
			],
			[
				'Calling md5() is forbidden.',
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
