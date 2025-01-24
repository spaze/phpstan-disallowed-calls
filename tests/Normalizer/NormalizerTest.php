<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Normalizer;

use PHPStan\Testing\PHPStanTestCase;

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


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
