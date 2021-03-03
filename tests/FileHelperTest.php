<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Generator;
use PHPUnit\Framework\TestCase;
use PHPStan\File\FileHelper as PHPStanFileHelper;

class FileHelperTest extends TestCase
{

	/** @var FileHelper */
	private $fileHelper;


	public function setUp(): void
	{
		$this->fileHelper = new FileHelper(new PHPStanFileHelper(__DIR__));
	}


	/**
	 * @param string $input
	 * @param string $output
	 * @dataProvider pathProvider
	 */
	public function testAbsolutizePath(string $input, string $output): void
	{
		$this->assertSame($output, $this->fileHelper->absolutizePath($input));
	}


	public function pathProvider(): Generator
	{
		yield ['src', __DIR__ . '/src'];
		yield ['src/*', __DIR__ . '/src/*'];
		yield ['*/src', '*/src'];
	}

}
