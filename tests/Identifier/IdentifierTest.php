<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

use Generator;
use PHPStan\Testing\PHPStanTestCase;

class IdentifierTest extends PHPStanTestCase
{

	/** @var Identifier */
	private $identifier;


	protected function setUp(): void
	{
		$this->identifier = new Identifier();
	}


	/**
	 * @param string $pattern
	 * @param string $value
	 * @return void
	 * @dataProvider matchesProvider
	 */
	public function testMatches(string $pattern, string $value): void
	{
		$this->assertTrue($this->identifier->matches($pattern, $value));
	}


	/**
	 * @param string $pattern
	 * @param string $value
	 * @return void
	 * @dataProvider doesNotMatchProvider
	 */
	public function testDoesNotMatch(string $pattern, string $value): void
	{
		$this->assertFalse($this->identifier->matches($pattern, $value));
	}


	public static function matchesProvider(): Generator
	{
		yield ['foo', 'foo'];
		yield ['foo', 'Foo'];
		yield ['foo\\bar', 'foo\\bar'];
		yield ['foo\\*', 'Foo\\Bar'];
	}


	public static function doesNotMatchProvider(): Generator
	{
		yield ['foo', 'bar'];
		yield ['foo\\*', 'Bar\\Foo'];
	}

}
