<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\ControlStructures;

use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\RuleTestCase;
use Spaze\PHPStan\Rules\Disallowed\DisallowedKeywordFactory;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedKeywordRuleErrors;

/**
 * @extends RuleTestCase<MatchControlStructure>
 */
class MatchControlStructureTest extends RuleTestCase
{

	/**
	 * @throws ShouldNotHappenException
	 */
	protected function getRule(): Rule
	{
		$container = self::getContainer();
		return new MatchControlStructure(
			$container->getByType(DisallowedKeywordRuleErrors::class),
			$container->getByType(DisallowedKeywordFactory::class)->getDisallowedKeywords([
				[
					'controlStructure' => 'match',
					'allowIn' => [
						__DIR__ . '/../src/disallowed-allow/*.php',
						__DIR__ . '/../src/*-allow/*.*',
					],
					'errorTip' => [
						'Mix',
						'Match',
					],
				],
			])
		);
	}


	public function testRule(): void
	{
		// Based on the configuration above, in this file:
		$this->analyse([__DIR__ . '/../src/disallowed/controlStructures.php'], [
			[
				// expect this error message:
				'Using the match control structure is forbidden.',
				// on this line:
				112,
				// with this tip:
				"• Mix\n• Match",
			],
		]);
		$this->analyse([__DIR__ . '/../src/disallowed-allow/controlStructures.php'], []);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
