<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Formatter;

use PHPStan\Testing\PHPStanTestCase;

class FormatterTest extends PHPStanTestCase
{

	/** @var Formatter */
	private $formatter;


	protected function setUp(): void
	{
		$this->formatter = self::getContainer()->getByType(Formatter::class);
	}


	public function testFormat(): void
	{
		$this->assertSame('foo', $this->formatter->formatIdentifier(['\\foo']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['foo', 'bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['foo', '\\bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['\\foo', 'bar']));
		$this->assertSame('{foo,bar}', $this->formatter->formatIdentifier(['\\foo', '\\bar']));
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
