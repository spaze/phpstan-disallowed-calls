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
