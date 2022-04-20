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


	public function superglobalsProvider(): Generator
	{
		yield ['$GLOBALS', null, null];
		yield ['$_SERVER', null, null];
		yield ['$_GET', null, null];
		yield ['$_POST', null, null];
		yield ['$_FILES', null, null];
		yield ['$_COOKIE', null, null];
		yield ['$_SESSION', null, null];
		yield ['$_REQUEST', null, null];
		yield ['$_ENV', null, null];
		yield ['$foo', ShouldNotHappenException::class, '$foo is not a superglobal variable'];
	}

}
