<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\TipRuleError;
use PHPStan\Testing\PHPStanTestCase;

class ErrorTipsTest extends PHPStanTestCase
{

	private ErrorTips $errorTips;


	protected function setUp(): void
	{
		$this->errorTips = self::getContainer()->getByType(ErrorTips::class);
	}


	public function testAddOne(): void
	{
		$errorBuilder = RuleErrorBuilder::message('foo');
		$this->errorTips->add(['a tip', ''], $errorBuilder);
		$ruleError = $errorBuilder->build();
		$this->assertInstanceOf(TipRuleError::class, $ruleError);
		$this->assertSame('a tip', $ruleError->getTip());
	}


	public function testAddMultiple(): void
	{
		$errorBuilder = RuleErrorBuilder::message('foo');
		$this->errorTips->add(['a tip', '', 'another one'], $errorBuilder);
		$ruleError = $errorBuilder->build();
		$this->assertInstanceOf(TipRuleError::class, $ruleError);
		$this->assertSame("• a tip\n• another one", $ruleError->getTip());
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
