<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use Generator;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;

class DisallowedSuperglobalFactoryTest extends PHPStanTestCase
{

	/**
	 * @dataProvider superglobalsProvider
	 * @param string $superglobal
	 * @param class-string|null $exceptionClass
	 * @throws ShouldNotHappenException
	 */
	public function testNonSuperglobalInConfig(string $superglobal, ?string $exceptionClass)
	{
		if ($exceptionClass) {
			$this->expectException($exceptionClass);
			$this->expectExceptionMessage("{$superglobal} is not a superglobal variable");
		} else {
			$this->expectNotToPerformAssertions();
		}
		(new DisallowedSuperglobalFactory())->getDisallowedVariables([['superglobal' => $superglobal]]);
	}


	public static function superglobalsProvider(): Generator
	{
		yield ['$GLOBALS', null];
		yield ['$_SERVER', null];
		yield ['$_GET', null];
		yield ['$_POST', null];
		yield ['$_FILES', null];
		yield ['$_COOKIE', null];
		yield ['$_SESSION', null];
		yield ['$_REQUEST', null];
		yield ['$_ENV', null];
		yield ['$foo', ShouldNotHappenException::class];
	}

}
