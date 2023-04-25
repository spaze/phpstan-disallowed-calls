<?php
declare(strict_types = 1);

namespace Normalizer;

use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class NormalizerTest extends PHPStanTestCase
{

	/** @var Normalizer */
	private $normalizer;


	protected function setUp(): void
	{
		$this->normalizer = new Normalizer();
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

}
