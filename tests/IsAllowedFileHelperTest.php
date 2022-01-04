<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Generator;
use PHPStan\File\FileHelper;
use PHPUnit\Framework\TestCase;

class IsAllowedFileHelperTest extends TestCase
{

	/** @var IsAllowedFileHelper */
	private $isAllowedHelper;


	protected function setUp(): void
	{
		$this->isAllowedHelper = new IsAllowedFileHelper(new FileHelper(__DIR__));
	}


	/**
	 * @param string $input
	 * @param string $output
	 * @dataProvider pathProvider
	 */
	public function testAbsolutizePath(string $input, string $output): void
	{
		$this->assertSame($output, $this->isAllowedHelper->absolutizePath($input));
	}


	public function pathProvider(): Generator
	{
		yield ['src', __DIR__ . '/src'];
		yield ['src/*', __DIR__ . '/src/*'];
		yield ['../src/*', str_replace(basename(__DIR__) . '/../', '', __DIR__ . '/../src/*')];
		yield ['src/foo/../*', __DIR__ . '/src/*'];
		yield ['*/src', '*/src'];
		yield ['*/../src', '*/../src'];
	}

}
