<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedSuperglobalFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedVariableRuleErrors;

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
				],
				// test disallowed paths
				[
					'superglobal' => '$_REQUEST',
					'message' => 'so $_GET or $_POST?',
					'disallowIn' => [
						__DIR__ . '/../src/disallowed/*.php',
					],
				],
			]),
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
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/superglobalUsages.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
