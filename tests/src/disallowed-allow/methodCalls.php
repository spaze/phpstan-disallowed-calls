<?php
declare(strict_types = 1);

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

// instantiation allowed by path
use Constructor\ClassWithConstructor;
use Constructor\ClassWithoutConstructor;

new ClassWithConstructor();
new ClassWithoutConstructor();
