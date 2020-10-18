<?php
declare(strict_types = 1);

use Constructor\ClassWithConstructor;
use Waldo\Quux;

$blade = new Quux\Blade();

// disallowed method
$blade->runner();
$blade->runner(42, true, '909');

// disallowed method, path and params don't match
$blade->runner(42, true, '808');

// not a disallowed method
$blade->server();

$sub = new Inheritance\Sub();

// disallowed parent method
$sub->x();

// disallowed trait methods
$testClass = new Traits\TestClass();
$testClass->x();
$testClassToo = new Traits\AnotherTestClass();
$testClassToo->y();
$testClassToo->zzTop();

// disallowed object creation
new ClassWithConstructor();
// phpcs:ignore PSR12.Classes.ClassInstantiation.MissingParentheses, SlevomatCodingStandard.ControlStructures.NewWithParentheses.MissingParentheses
new Constructor\ClassWithoutConstructor;

// allowed object creation
new stdClass();
