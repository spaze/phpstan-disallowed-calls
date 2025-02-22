<?php
declare(strict_types = 1);

namespace Superglobals;

use Attributes\AttributeClass;
use Attributes\AttributeColumn2;

class Superglobals
{
}

#[AttributeClass]
class ChildSuperglobals extends Superglobals
{

	#[AttributeClass]
	public function leMethod(): void
	{
		$GLOBALS;
		$_SERVER;
		$_GET;
		$_POST;
		$_FILES;
		$_COOKIE;
		$_SESSION;
		$_ENV;
	}


	#[\AttributeClass2]
	private function method()
	{
	}

}

#[AttributeColumn2]
class Superglobals2
{

	#[AttributeColumn2]
	public function leMethod(): void
	{
		$GLOBALS;
		$_SERVER;
		$_GET;
		$_POST;
		$_FILES;
		$_COOKIE;
		$_SESSION;
		$_ENV;
	}

}
