<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Generator;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\File\FileHelper;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use Traits\TestClass;
use Traits\TestTrait;

class IsAllowedFileHelperTest extends PHPStanTestCase
{

	/** @var AllowedPath */
	private $isAllowedHelper;

	/** @var AllowedPath */
	private $isAllowedHelperWithRootDir;

	/** @var ScopeFactory */
	private $scopeFactory;

	/** @var ReflectionProvider */
	private $reflectionProvider;


	protected function setUp(): void
	{
		$this->isAllowedHelper = new AllowedPath(new FileHelper(__DIR__));
		$this->isAllowedHelperWithRootDir = new AllowedPath(new FileHelper(__DIR__), '/foo/bar');
		$this->reflectionProvider = $this->createReflectionProvider();
		$this->scopeFactory = $this->createScopeFactory($this->reflectionProvider, self::getContainer()->getService('typeSpecifier'));
	}


	/**
	 * @dataProvider pathProvider
	 */
	public function testMatches(string $allowedPath, string $file, string $fileWithRootDir): void
	{
		$context = ScopeContext::create($file);
		$this->assertTrue($this->isAllowedHelper->matches($this->scopeFactory->create($context), $allowedPath));
		$context = ScopeContext::create($fileWithRootDir);
		$this->assertTrue($this->isAllowedHelperWithRootDir->matches($this->scopeFactory->create($context), $allowedPath));
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
		$context = ScopeContext::create($classReflection->getFileName())->enterClass($classReflection)->enterTrait($traitReflection);
		$this->assertTrue($this->isAllowedHelper->matches($this->scopeFactory->create($context), $traitReflection->getFileName()));
	}

}
