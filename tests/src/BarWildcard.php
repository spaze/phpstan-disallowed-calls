<?php
declare(strict_types = 1);

namespace Waldo\Foo;

interface WildInterface
{
}

class WildBase
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}

class ChildOfBase extends WildBase
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}

class GrandChildOfBase extends ChildOfBase
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}

class ImplementsWildInterface implements WildInterface
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}

class InheritsWildInterface extends ImplementsWildInterface
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}

class NotWild
{

	public function inWild(): void
	{
		str_pad('foo', 10);
		str_repeat('bar', 3);
	}

}
