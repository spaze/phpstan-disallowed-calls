<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use Generator;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\File\FileHelper;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spaze\PHPStan\Rules\Disallowed\File\FilePath;
use Traits\TestClass;
use Traits\TestTrait;

class AllowedPathTest extends PHPStanTestCase
{

	private AllowedPath $allowedPath;

	private AllowedPath $allowedPathWithRootDir;

	private ScopeFactory $scopeFactory;

	private ReflectionProvider $reflectionProvider;


	protected function setUp(): void
	{
		/** @phpstan-ignore phpstanApi.constructor (Can't get the instance from the DI container because it has the workingDirectory set to the .phar path) */
		$fileHelper = new FileHelper(__DIR__);
		$this->allowedPath = new AllowedPath(new FilePath($fileHelper));
		$this->allowedPathWithRootDir = new AllowedPath(new FilePath($fileHelper, '/foo/bar'));
		$this->reflectionProvider = $this->createReflectionProvider();
		$this->scopeFactory = $this->createScopeFactory($this->reflectionProvider, self::getContainer()->getByType(TypeSpecifier::class));
	}


	/**
	 * @dataProvider pathProvider
	 */
	#[DataProvider('pathProvider')]
	public function testMatches(string $allowedPath, string $file, string $fileWithRootDir): void
	{
		$context = ScopeContext::create($file);
		$this->assertTrue($this->allowedPath->matches($this->scopeFactory->create($context), $allowedPath));
		$context = ScopeContext::create($fileWithRootDir);
		$this->assertTrue($this->allowedPathWithRootDir->matches($this->scopeFactory->create($context), $allowedPath));
	}


	/**
	 * @return Generator<int, array{0:string, 1:string, 2:string}>
	 */
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


	/**
	 * @throws ShouldNotHappenException
	 */
	public function testMatchesInTraits(): void
	{
		$classReflection = $this->reflectionProvider->getClass(TestClass::class);
		$traitReflection = $this->reflectionProvider->getClass(TestTrait::class);
		$classFile = $classReflection->getFileName();
		assert($classFile !== null);
		$context = ScopeContext::create($classFile);
		/** @phpstan-ignore phpstanApi.method, phpstanApi.method (🤞 for both methods) */
		$context = $context->enterClass($classReflection)->enterTrait($traitReflection);
		$traitFile = $traitReflection->getFileName();
		assert($traitFile !== null);
		$this->assertTrue($this->allowedPath->matches($this->scopeFactory->create($context), $traitFile));
	}

}
