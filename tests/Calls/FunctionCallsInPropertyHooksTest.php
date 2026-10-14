<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Calls;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCallFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedCallableParameterRuleErrors;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedFunctionRuleErrors;

/**
 * @requires PHP >= 8.4.0
 * @extends RuleTestCase<FunctionCalls>
 */
#[RequiresPhp('>= 8.4.0')]
class FunctionCallsInPropertyHooksTest extends RuleTestCase
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
					'allowInMethodsWithAttributes' => [
						'PropertyHooks\AllowMd5',
					],
				],
				[
					'function' => 'sha1()',
					'allowExceptInMethodsWithAttributes' => [
						'PropertyHooks\DisallowSha1',
					],
				],
				[
					'function' => 'crc32()',
					'allowInClassWithMethodAttributes' => [
						'PropertyHooks\AllowCrc32',
					],
				],
				[
					'function' => 'strlen()',
					'allowExceptInClassWithMethodAttributes' => [
						'PropertyHooks\DenyStrlen',
					],
				],
				[
					'function' => 'ucfirst()',
					'allowInMethods' => [
						'PropertyHooks\NamedHooks::$named::get()',
					],
				],
				[
					'function' => 'lcfirst()',
					'allowExceptInMethods' => [
						'PropertyHooks\*::$named::set()',
					],
				],
			]
		);
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/PropertyHooks.php'], [
			[
				'Calling md5() is forbidden.',
				15,
			],
			[
				'Calling md5() is forbidden.',
				17,
			],
			[
				'Calling sha1() is forbidden.',
				23,
			],
			[
				'Calling md5() is forbidden.',
				31,
			],
			[
				'Calling strlen() is forbidden.',
				82,
			],
			[
				'Calling crc32() is forbidden.',
				97,
			],
			[
				'Calling ucfirst() is forbidden.',
				112,
			],
			[
				'Calling lcfirst() is forbidden.',
				113,
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
