<?php
declare(strict_types = 1);

use Exceptions\TestException;
use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

// disallowed based on definedIn
$blade = new Blade();
$blade->andSorcery();
$blade->server();

// allowed because these are defined elsewhere
$bar = new Bar();
$bar->foo();
$bar->bar();

// allowed because it's a built-in function
time();

// allowed because it's a built-in class
$exception = new Exception();
$exception->getMessage();
$exception->getPrevious();

// allowed because it's defined elsewhere
$testException = new TestException();
$testException->getMessage();
$testException->getPrevious();
