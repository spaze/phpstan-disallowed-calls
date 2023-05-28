<?php
declare(strict_types = 1);

use Waldo\Foo\Bar;
use Waldo\Quux\Blade;

// allowed by path
$blade = new Blade();
$blade->andSorcery();
$blade->server();

// allowed by path
$bar = new Bar();
$bar->foo();
$bar->bar();

// allowed because it's a built-in function
time();
