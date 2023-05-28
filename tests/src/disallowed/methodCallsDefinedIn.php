<?php
declare(strict_types = 1);

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
