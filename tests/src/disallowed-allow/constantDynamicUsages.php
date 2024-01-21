<?php
declare(strict_types = 1);

use Waldo\Quux\Blade;

// allowed by path
$kind = 'RUNNER';
echo Blade::{$kind};
/** @var 'DECKARD'|'MOVIE'|'RUNNER' $kind2 */
echo Blade::{$kind2};
