<?php
declare(strict_types = 1);

use Inheritance\Base;
use Waldo\Quux\Blade;

// disallowed
FILTER_FLAG_NO_PRIV_RANGE;
\FILTER_FLAG_NO_PRIV_RANGE;
FILTER_FLAG_NO_RES_RANGE;
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
$orion::WESLEY;

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

// disallowed by path
PHP_EOL;

// disallowed
$foo = new class extends Base {};
$foo::BELONG;
