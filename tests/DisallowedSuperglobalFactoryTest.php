<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Generator;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DisallowedSuperglobalFactoryTest extends PHPStanTestCase
{

	/**
	 * @dataProvider superglobalsProvider
	 * @param string $superglobal
	 * @param class-string<ShouldNotHappenException>|null $exceptionClass
	 * @throws ShouldNotHappenException
	 */
	#[DataProvider('superglobalsProvider')]
	public function testNonSuperglobalInConfig(string $superglobal, string $superglobalQuoted, ?string $exceptionClass): void
	{
		if ($exceptionClass) {
			$this->expectException($exceptionClass);
			$this->expectExceptionMessageMatches("~{$superglobalQuoted} is not a superglobal variable~");
		} else {
			$this->expectNotToPerformAssertions();
		}
		self::getContainer()->getByType(DisallowedSuperglobalFactory::class)->getDisallowedVariables([['superglobal' => $superglobal]]);
	}


	/**
	 * @return Generator<int, array{0:string, 1:string, class-string<ShouldNotHappenException>|null}>
	 */
	public static function superglobalsProvider(): Generator
	{
		yield ['$GLOBALS', '\$GLOBALS', null];
		yield ['$_SERVER', '\$_SERVER', null];
		yield ['$_GET', '\$_GET', null];
		yield ['$_POST', '\$_POST', null];
		yield ['$_FILES', '\$_FILES', null];
		yield ['$_COOKIE', '\$_COOKIE', null];
		yield ['$_SESSION', '\$_SESSION', null];
		yield ['$_REQUEST', '\$_REQUEST', null];
		yield ['$_ENV', '\$_ENV', null];
		yield ['$foo', '\$foo', ShouldNotHappenException::class];
	}


	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../extension.neon',
		];
	}

}
