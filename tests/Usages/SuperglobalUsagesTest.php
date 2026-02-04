<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedSuperglobalFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedVariableRuleErrors;

/**
 * @extends RuleTestCase<VariableUsages>
 */
class SuperglobalUsagesTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new VariableUsages(
			$container->getByType(DisallowedVariableRuleErrors::class),
			$container->getByType(DisallowedSuperglobalFactory::class)->getDisallowedVariables([
				[
					'superglobal' => '$GLOBALS',
					'message' => 'the cake is a lie',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowInInstanceOf' => [
						'\Superglobals\Superglobals',
					],
					'errorTip' => 'So long and thanks for all the tips',
				],
				[
					'superglobal' => [
						'$_GET',
						'$_POST',
					],
					'message' => 'the cake is a lie',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'allowInMethods' => [
						'\Superglobals\ChildSuperglobals::leMethod()',
					],
				],
				// test disallowed paths
				[
					'superglobal' => '$_REQUEST',
					'message' => 'so $_GET or $_POST?',
					'disallowIn' => [
						__DIR__ . '/../src/disallowed/*.php',
					],
					'errorTip' => [
						'Choose one',
						'Or the other',
					],
				],
				// test allowed instances
				[
					'superglobal' => '$_SERVER',
					'allowExceptInInstanceOf' => [
						'\Superglobals\Superglobals2',
					],
				],
				// test allowed attributes
				[
					'superglobal' => '$_FILES',
					'allowInMethodsWithAttributes' => [
						'\Attributes\AttributeClass',
					],
				],
				[
					'superglobal' => '$_COOKIE',
					'allowExceptInMethodsWithAttributes' => [
						'\Attributes\AttributeColumn2',
					],
				],
				[
					'superglobal' => '$_SESSION',
					'allowInClassWithAttributes' => [
						'\Attributes\AttributeClass',
					],
				],
				[
					'superglobal' => '$_ENV',
					'allowInClassWithMethodAttributes' => [
						'AttributeClass2',
					],
				],
			])
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/superglobalUsages.php'], [
			[
				// expect this error message:
				'Using $GLOBALS is forbidden, the cake is a lie.',
				// on this line:
				8,
				'So long and thanks for all the tips',
			],
			[
				'Using $_GET is forbidden, the cake is a lie.',
				9,
			],
			[
				'Using $_GET is forbidden, the cake is a lie.',
				12,
			],
			[
				'Using $_POST is forbidden, the cake is a lie.',
				13,
			],
			[
				'Using $_REQUEST is forbidden, so $_GET or $_POST?',
				26,
				"• Choose one\n• Or the other",
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/superglobalUsages.php'], []);
	}


	public function testRuleAllow(): void
	{
		$this->analyse([__DIR__ . '/../src/Superglobals.php'], [
			[
				'Using $GLOBALS is forbidden, the cake is a lie.',
				45,
				'So long and thanks for all the tips',
			],
			[
				'Using $_SERVER is forbidden.',
				46,
			],
			[
				'Using $_GET is forbidden, the cake is a lie.',
				47,
			],
			[
				'Using $_POST is forbidden, the cake is a lie.',
				48,
			],
			[
				'Using $_FILES is forbidden.',
				49,
			],
			[
				'Using $_COOKIE is forbidden.',
				50,
			],
			[
				'Using $_SESSION is forbidden.',
				51,
			],
			[
				'Using $_ENV is forbidden.',
				52,
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
