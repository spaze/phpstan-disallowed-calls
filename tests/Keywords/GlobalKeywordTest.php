<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Keywords;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedKeywordFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedKeywordRuleErrors;

/**
 * @extends RuleTestCase<GlobalKeyword>
 */
class GlobalKeywordTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new GlobalKeyword(
			$container->getByType(DisallowedKeywordRuleErrors::class),
			$container->getByType(DisallowedKeywordFactory::class)->getDisallowedKeywords([
				[
					'keyword' => 'global',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
				],
			])
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/keywords.php'], [
			[
				// expect this error message:
				'Using the global keyword is forbidden.',
				// on this line:
				7,
			],
			[
				'Using the global keyword is forbidden.',
				8,
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/keywords.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
