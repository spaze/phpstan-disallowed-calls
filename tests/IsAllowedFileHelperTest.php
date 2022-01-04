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

	/** @var IsAllowedFileHelper */
	private $isAllowedHelperWithRootDir;


	protected function setUp(): void
	{
		$this->isAllowedHelper = new IsAllowedFileHelper(new FileHelper(__DIR__));
		$this->isAllowedHelperWithRootDir = new IsAllowedFileHelper(new FileHelper(__DIR__), '/foo/bar');
	}


	/**
	 * @param string $input
	 * @param string $output
	 * @dataProvider pathProvider
	 */
	public function testAbsolutizePath(string $input, string $output, string $outputWithDir): void
	{
		$this->assertSame($output, $this->isAllowedHelper->absolutizePath($input));
		$this->assertSame($outputWithDir, $this->isAllowedHelperWithRootDir->absolutizePath($input));
	}


	public function pathProvider(): Generator
	{
		yield [
			'src',
			__DIR__ . '/src',
			'/foo/bar/src',
		];
		yield [
			'src/*',
			__DIR__ . '/src/*',
			'/foo/bar/src/*',
		];
		yield [
			'../src/*',
			str_replace(basename(__DIR__) . '/../', '', __DIR__ . '/../src/*'),
			'/foo/src/*',
		];
		yield [
			'src/foo/../*',
			__DIR__ . '/src/*',
			'/foo/bar/src/*',
		];
		yield [
			'*/src',
			'*/src',
			'*/src',
		];
		yield [
			'*/../src',
			'*/../src',
			'*/../src',
		];
		yield [
			'\\src\\foo\\bar\\',
			__DIR__ . '/src/foo/bar',
			'/foo/bar/src/foo/bar',
		];
	}

}
