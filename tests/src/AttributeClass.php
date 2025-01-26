<?php
declare(strict_types = 1);

namespace Attributes;

use Attribute;

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
	}

}

#[Attribute3]
class AttributeClass2
{

	#[Attribute5]
	public function method(): void
	{
		strlen('anazgoh');
	}

}

class ChildAttributeClass extends AttributeClass
{

	#[Attribute4]
	public function method(): void
	{
		md5('QNKCDZO');
		strlen('anazgoh');
	}

}

class ChildAttributeClass2
{

	#[Attribute5]
	public function method(): void
	{
		strlen('anazgoh');
	}

}
