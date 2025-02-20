<?php
declare(strict_types = 1);

namespace Waldo\Foo;

use Attributes\AttributeClass;
use Attributes\AttributeColumn2;
use DateTimeImmutable;
use DateTimeZone;

class Bar
{

	public const NAME = 'Bar';


	public function foo(): void
	{
	}


	public function bar(): void
	{
	}

}

class BarChild extends Bar
{

	public ?DateTimeZone $a;
	public ?DateTimeImmutable $b;

	#[AttributeClass, AttributeColumn2]
	public function inInstanceOf(DateTimeZone $a, DateTimeImmutable $b): void
	{
		$el = simplexml_load_string('<foo/>');
		\Dom\import_simplexml($el);
		$zone = new (DateTimeZone::class)('Europe/Prague');
		$zone->getLocation();
		$date = new DateTimeImmutable();
		$date->format('Y');
	}

}

class Bar2
{

	public ?DateTimeZone $a;
	public ?DateTimeImmutable $b;

	#[AttributeClass, AttributeColumn2]
	public function inInstanceOf(DateTimeZone $a, DateTimeImmutable $b): void
	{
		$el = simplexml_load_string('<foo/>');
		\Dom\import_simplexml($el);
		$zone = new DateTimeZone('Europe/Prague');
		$zone->getLocation();
		$date = new(DateTimeImmutable::class)();
		$date->format('Y');
	}

}

class BarInterface implements \Stringable
{

	public ?DateTimeZone $a;
	public ?DateTimeImmutable $b;

	#[AttributeClass, AttributeColumn2]
	public function inInstanceOf(DateTimeZone $a, DateTimeImmutable $b): void
	{
		$el = simplexml_load_string('<foo/>');
		\Dom\import_simplexml($el);
		$zone = new (DateTimeZone::class)('Europe/Prague');
		$zone->getLocation();
		$date = new DateTimeImmutable();
		$date->format('Y');
	}


	public function __toString(): string
	{
		return '';
	}

}
