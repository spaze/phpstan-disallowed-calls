<?php
declare(strict_types = 1);

// all allowed by path

$callable = print_r(...);
$callable(42);

$blade = new \Waldo\Quux\Blade();
$callable = $blade->runner(...);
$callable(303);
$method = 'runner';
$callable = $blade->$method(...);
$callable(808);

$callable = \Fiction\Pulp\Royale::withoutCheese(...);
$callable(303);
$method = 'withoutCheese';
$callable = \Fiction\Pulp\Royale::$method(...);
$callable(808);

$class = \Fiction\Pulp\Royale::class;
$callable = $class::withoutCheese(...);
$callable(303);
$method = 'withoutCheese';
$callable = $class::$method(...);
$callable(808);
