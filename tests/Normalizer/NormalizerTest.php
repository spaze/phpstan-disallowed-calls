<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Normalizer;

use Generator;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class NormalizerTest extends PHPStanTestCase
{

	private Normalizer $normalizer;


	protected function setUp(): void
	{
		$this->normalizer = self::getContainer()->getByType(Normalizer::class);
	}


	public function testNormalizeCall(): void
	{
		$this->assertSame('foo\\bar::baz', $this->normalizer->normalizeCall('\\foo\\bar::baz()'));
	}


	public function testNormalizeNamespace(): void
	{
		$this->assertSame('foo', $this->normalizer->normalizeNamespace('foo'));
		$this->assertSame('foo', $this->normalizer->normalizeNamespace('\\foo'));
		$this->assertSame('foo', $this->normalizer->normalizeNamespace('\\\\foo'));
		$this->assertSame('foo\\bar', $this->normalizer->normalizeNamespace('\\foo\\bar'));
	}


	public function testNormalizeAttribute(): void
	{
		$this->assertSame('foo', $this->normalizer->normalizeAttribute('foo'));
		$this->assertSame('foo', $this->normalizer->normalizeAttribute('foo()'));
		$this->assertSame('foo', $this->normalizer->normalizeAttribute('\\foo()'));
		$this->assertSame('foo', $this->normalizer->normalizeAttribute('#[\\foo]'));
		$this->assertSame('foo', $this->normalizer->normalizeAttribute('#[\\foo()]'));
		$this->assertSame('foo\\bar', $this->normalizer->normalizeAttribute('#[\\foo\\bar()]'));
	}


	public function testNormalizeProperty(): void
	{
		$this->assertSame('foo::$bar', $this->normalizer->normalizeProperty('foo::bar'));
		$this->assertSame('foo::$bar', $this->normalizer->normalizeProperty('foo::$bar'));
		$this->assertSame('foo::$bar', $this->normalizer->normalizeProperty('\\foo::bar'));
		$this->assertSame('foo::$bar', $this->normalizer->normalizeProperty('\\foo::$bar'));
	}


	/**
	 * @return Generator<int, array{0:string, 1:string}>
	 */
	public static function propertyProvider(): Generator
	{
		yield [
			'foo',
			"Property 'foo' is invalid, use 'Namespace\\Class::\$property' syntax",
		];
		yield [
			'\\foo',
			"Property '\\foo' is invalid, use 'Namespace\\Class::\$property' syntax",
		];
		yield [
			'foo:$bar',
			"Property 'foo:\$bar' is invalid, use 'Namespace\\Class::\$property' syntax",
		];
		yield [
			'foo:$bar:baz',
			"Property 'foo:\$bar:baz' is invalid, use 'Namespace\\Class::\$property' syntax",
		];
	}


	/**
	 * @dataProvider propertyProvider
	 */
	#[DataProvider('propertyProvider')]
	public function testNormalizeInvalidProperty(string $property, string $exceptionMessage): void
	{
		$this->expectException(ShouldNotHappenException::class);
		$this->expectExceptionMessage($exceptionMessage);
		$this->normalizer->normalizeProperty($property);
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
