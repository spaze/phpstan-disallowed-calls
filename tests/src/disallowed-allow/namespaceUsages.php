<?php
declare(strict_types = 1);

namespace Whatever;

use Framework\SomeInterface;
use Inheritance\Base;
use Inheritance\Sub;
use Traits\TestTrait;
use Waldo\Quux\Blade;
use Waldo\Foo\Bar;

class Service extends Base implements SomeInterface
{
	private $blade;

	use TestTrait;


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

}
