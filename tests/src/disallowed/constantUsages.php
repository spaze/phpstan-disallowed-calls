<?php
declare(strict_types = 1);

use Inheritance\Base;
use Waldo\Quux\Blade;

// disallowed
FILTER_FLAG_NO_PRIV_RANGE;
\FILTER_FLAG_NO_PRIV_RANGE;
\Inheritance\Sub::BELONG;
\Inheritance\Base::BELONG;
Base::BELONG;
Blade::RUNNER;
\Waldo\Quux\Blade::RUNNER;

$quux = 'Quux';
$blade = "\\Waldo\\{$quux}\\Blade";
$blade::DECKARD;

$orion = new Blade();
$orion::DECKARD;

// not a disallowed constant usage
Base::ALL;

// not a constant
Base::class;
\Constructor\ClassWithConstructor::class;

// types that support generics
/**
 * @var PhpOption\None<string> $none
 */
$none = PhpOption\None::create();
$none::NAME;

// disallowed constants with class wildcard
echo DateTime::ISO8601;
echo DateTimeImmutable::ISO8601;
echo DateTimeInterface::ISO8601;

// disallowed class constants with wildcard in constant
echo DateTimeInterface::RFC1123;
echo DateTimeInterface::RFC3339;

// global constant wildcard
echo FILTER_FLAG_ALLOW_FRACTION;
