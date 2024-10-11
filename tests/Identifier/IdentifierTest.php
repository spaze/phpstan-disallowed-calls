<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

use Generator;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class IdentifierTest extends PHPStanTestCase
{

	private Identifier $identifier;


	protected function setUp(): void
	{
		$this->identifier = self::getContainer()->getByType(Identifier::class);
	}


	/**
	 * @param string $pattern
	 * @param string $value
	 * @param list<string>|null $excludes
	 * @return void
	 * @dataProvider matchesProvider
	 */
	#[DataProvider('matchesProvider')]
	public function testMatches(string $pattern, string $value, ?array $excludes): void
	{
		$this->assertTrue($this->identifier->matches($pattern, $value, $excludes));
	}


	/**
	 * @param string $pattern
	 * @param string $value
	 * @param list<string>|null $excludes
	 * @return void
	 * @dataProvider doesNotMatchProvider
	 */
	#[DataProvider('doesNotMatchProvider')]
	public function testDoesNotMatch(string $pattern, string $value, ?array $excludes): void
	{
		$this->assertFalse($this->identifier->matches($pattern, $value, $excludes));
	}


	public static function matchesProvider(): Generator
	{
		yield ['foo', 'foo', []];
		yield ['foo', 'Foo', []];
		yield ['foo\\*', 'Foo\\Bar', []];
		yield ['foo\\bar', 'foo\\bar', []];
		yield ['foo\\bar', 'Foo\\Bar', []];
		yield ['foo\\bar', 'foo\\bar', ['bar*']];
		yield ['foo\\bar', 'foo\\bar', ['n*pe', 'bar\\*']];
	}


	public static function doesNotMatchProvider(): Generator
	{
		yield ['foo', 'bar', []];
		yield ['foo', 'foo', ['foo']];
		yield ['foo', 'Foo', ['foo']];
		yield ['foo', 'Foo', ['fOO']];
		yield ['foo', 'Foo', ['f*']];
		yield ['foo', 'Foo', ['F*']];
		yield ['foo\\*', 'Bar\\Foo', []];
		yield ['foo\\bar', 'foo\\bar', ['foo*']];
		yield ['foo\\bar', 'foo\\bar', ['n*pe', 'foo\\*']];
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
