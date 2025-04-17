<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class Reproducer2Test extends RuleTestCase
{

	protected function shouldNarrowMethodScopeFromConstructor(): bool
	{
		return true;
	}


	protected function getRule(): Rule
	{
		return new Reproducer2Usages();
	}


	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../src/Reproducer2Class.php'], [
			[
				'class DateTime found in method null',
				12,
			],
			[
				'class Baz\Waldo found in method null',
				16,
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
