<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Testing\PHPStanTestCase;

class IdentifierFormatterTest extends PHPStanTestCase
{

	/** @var IdentifierFormatter */
	private $identifierFormatter;


	protected function setUp(): void
	{
		$this->identifierFormatter = new IdentifierFormatter();
	}


	public function testFormat(): void
	{
		$this->assertSame('foo', $this->identifierFormatter->format(['foo']));
		$this->assertSame('{foo,bar}', $this->identifierFormatter->format(['foo', 'bar']));
	}

}
