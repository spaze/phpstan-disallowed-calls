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

use Constructor\ClassWithConstructor;
use Constructor\ClassWithoutConstructor;

class MethodsWithAttributes1
{

	#[\Attribute7]
	public function attributes1(): void
	{
		var_dump(1337);
		print_r(1338);
		new ClassWithConstructor();
		new ClassWithoutConstructor();
	}


	#[\Attribute8]
	public function attributes2(): void
	{
	}

}

class MethodsWithAttributes2
{

	#[\Attribute7]
	public function method1(): void
	{
		var_dump(1337);
		print_r(1338);
		new ClassWithConstructor();
		new ClassWithoutConstructor();
	}


	#[\Attribute6]
	private function method2(): void
	{
	}


	#[\Attribute8]
	protected function method3(): void
	{
	}


	#[\Attribute7, \Attribute9]
	final public static function method4(): void
	{
	}


	#[\Attribute7, \Attribute10]
	public function method5(): void
	{
	}

}

use PhpOption\None;
use PhpOption\Some;

class CallsWithAttributes
{

	#[\Attribute10, \Attribute12]
	public function method1(None|Some $union, None $none, Some $some, Blade $foo, $bar): void
	{
		strtolower('');
		strtoupper('');
		new None();
		new Some(303);
		#[\Attribute12]
		function foo(): void {};
		#[\Attribute13]
		function bar(): void {};
	}


	#[\Attribute11, \Attribute13]
	public function method2(None|Some $union, None $none, Some $some, Blade $foo, $bar): void
	{
		strtolower('');
		strtoupper('');
		new None();
		new Some(303);
		#[\Attribute12]
		function foo(): void {};
		#[\Attribute13]
		function bar(): void {};
	}

}
