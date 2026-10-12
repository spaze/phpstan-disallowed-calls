<?php
declare(strict_types = 1);

namespace Enums;

enum Enum
{

	public const ENUM_CONST = true;
	case Foo;
	case Bar;
	case Baz;

}

Enum::ENUM_CONST;
Enum::Foo;
Enum::Bar;
Enum::Baz;

enum BackedEnum: int
{

	public const ENUM_CONST = true;
	case Waldo = 1;
	case Quux = 2;
	case Fred = 3;

}

BackedEnum::ENUM_CONST;
BackedEnum::Waldo;
BackedEnum::Quux;
BackedEnum::Fred;

function enumTypeHints(
	Enum $enum,
	?BackedEnum $backedEnum,
	Enum|\DateTimeImmutable $union,
): ?Enum {
	return null;
}

function enumStaticCalls(): void
{
	Enum::cases();
	BackedEnum::from(1);
}

function enumInstanceof(object $object): bool
{
	return $object instanceof Enum;
}

interface EnumInterface
{
}

enum InterfaceEnum implements EnumInterface
{
	case Foo;
}

function firstClassCallables(): void
{
	$cases = Enum::cases(...);
	$from = BackedEnum::from(...);
	$createFromFormat = \DateTimeImmutable::createFromFormat(...);
}
