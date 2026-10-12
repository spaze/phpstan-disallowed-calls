<?php
declare(strict_types = 1);

namespace ClassLikeLabels;

interface SomeInterface
{
	public const VALUE = 1;
}

trait SomeTrait
{
	public static function foo(): void
	{
	}
}

class SomeClass
{
}

function labels(SomeInterface $interface, SomeClass $class): void
{
	echo SomeInterface::VALUE;
	SomeTrait::foo();
	new SomeClass();
}
