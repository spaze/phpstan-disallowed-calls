<?php
declare(strict_types = 1);

use Constructor\ClassWithConstructor;
use Waldo\Quux;

$blade = new Quux\Blade();

// disallowed method
$blade->runner();
$blade->runner(42, true, '808');

// allowed by path and only with these params
$blade->runner(42, true, '909');

// not a disallowed method
$blade->server();

$sub = new Inheritance\Sub();

// parent method allowed by path
$sub->x();

// trait methods allowed by path
$testClass = new Traits\TestClass();
$testClass->x();
$testClassToo = new Traits\AnotherTestClass();
$testClassToo->y();
$testClassToo->zzTop();

// object creation allowed by path
new ClassWithConstructor();
// phpcs:ignore PSR12.Classes.ClassInstantiation.MissingParentheses, SlevomatCodingStandard.ControlStructures.NewWithParentheses.MissingParentheses
new Constructor\ClassWithoutConstructor;

// allowed object creation
new stdClass();
