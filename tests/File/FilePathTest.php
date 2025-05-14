<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\File;

use Generator;
use PHPStan\File\FileHelper;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FilePathTest extends PHPStanTestCase
{

	private FilePath $filePath;

	private FilePath $filePathWithRootDir;


	protected function setUp(): void
	{
		/** @phpstan-ignore phpstanApi.constructor (Can't get the instance from the DI container because it has the workingDirectory set to the .phar path) */
		$fileHelper = new FileHelper(__DIR__);
		$this->filePath = new FilePath($fileHelper);
		$this->filePathWithRootDir = new FilePath($fileHelper, '/foo/bar');
	}


	/**
	 * @dataProvider pathProvider
	 */
	#[DataProvider('pathProvider')]
	public function testFnMatch(string $path, string $file, string $fileWithRootDir): void
	{
		$this->assertTrue($this->filePath->fnMatch($path, $file));
		$this->assertTrue($this->filePathWithRootDir->fnMatch($path, $fileWithRootDir));
	}


	public static function pathProvider(): Generator
	{
		yield [
			'src',
			__DIR__ . '/src',
			'/foo/bar/src',
		];
		yield [
			'src/*',
			__DIR__ . '/src/waldo.php',
			'/foo/bar/src/waldo.php',
		];
		yield [
			'../src/*',
			str_replace(basename(__DIR__) . '/../', '', __DIR__ . '/../src/waldo.php'),
			'/foo/src/waldo.php',
		];
		yield [
			'src/foo/../*',
			__DIR__ . '/src/waldo.php',
			'/foo/bar/src/waldo.php',
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


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../extension.neon',
		];
	}

}
