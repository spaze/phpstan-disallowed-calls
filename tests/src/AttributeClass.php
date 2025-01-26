<?php
declare(strict_types = 1);

namespace Attributes;

use Attribute;
use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

#[Attribute]
#[Attribute2]
class AttributeClass
{

	#[Attribute4]
	public function method(): void
	{
		md5('QNKCDZO');
		sha1('aaroZmOk');
		strlen('anazgoh');
		Bar::NAME;
		(new Bar())->bar();
		Blade::RUNNER;
	}

}

#[Attribute3]
class AttributeClass2
{

	#[Attribute5]
	public function method(): void
	{
		strlen('anazgoh');
		(new Bar())->foo();
		Blade::RUNNER;
	}

}

class ChildAttributeClass extends AttributeClass
{

	#[Attribute4]
	public function method(): void
	{
		md5('QNKCDZO');
		strlen('anazgoh');
		(new Bar())->foo();
		Blade::RUNNER;
	}

}

class ChildAttributeClass2
{

	#[Attribute5]
	public function method(): void
	{
		strlen('anazgoh');
		(new Bar())->foo();
		Blade::RUNNER;
	}

}
