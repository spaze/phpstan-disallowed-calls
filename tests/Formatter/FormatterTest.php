<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Formatter;

use PHPStan\Testing\PHPStanTestCase;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;

class FormatterTest extends PHPStanTestCase
{

	/** @var Formatter */
	private $formatter;


	protected function setUp(): void
	{
		$this->formatter = new Formatter(new Normalizer());
	}


	public function testFormat(): void
	{
		$this->assertSame('foo', $this->formatter->formatIdentifier(['\\foo']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['foo', 'bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['foo', '\\bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['\\foo', 'bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['\\foo', '\\bar']));
	}

}
