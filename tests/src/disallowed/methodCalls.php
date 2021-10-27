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
$classname = Constructor\ClassWithoutConstructor::class;
new $classname();

// allowed object creation
new stdClass();

// types that support generics
/**
 * @var PhpOption\None<string> $none
 */
$none = PhpOption\None::create();
$none->getIterator();

/**
 * @var PhpOption\Some<string> $some
 */
$some = PhpOption\Some::create('value');
$some->getIterator();

// disallowed value in an otherwise allowed param
(new DateTime())->format('y');
(new DateTime())->format('Y');
new DateTime('tOmOrRoW');
