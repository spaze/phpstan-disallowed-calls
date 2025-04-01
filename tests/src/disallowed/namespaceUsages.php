<?php
declare(strict_types = 1);

namespace Whatever;

use Framework\SomeInterface;
use Inheritance\Base;
use Inheritance\Sub;
use PrivateModule\PrivateClass;
use PrivateModule\PublicClass;
use Traits\TestTrait;
use Waldo\Foo\Bar;
use Waldo\Quux\blade;
use ZipArchive;

class Service extends Base implements SomeInterface
{

	use TestTrait;


	private $blade;


	// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.MismatchingCaseSensitivity
	public function __construct(Blade $blade)
	{
		$this->blade = $blade;
	}


	public static function callSub()
	{
		Sub::woofer();
	}


	public function callConstant()
	{
		return Bar::NAME;
	}


	public function callNew()
	{
		return new Bar();
	}


	public function disallowedByPath()
	{
		return ZipArchive::EM_TRAD_PKWARE;
	}


	public function anonymousClass()
	{
		return new class extends Base {};
	}

	public function usePublicClass()
	{
		return new PublicClass();
	}

	public function usePrivateClass()
	{
		return new PrivateClass();
	}

}
