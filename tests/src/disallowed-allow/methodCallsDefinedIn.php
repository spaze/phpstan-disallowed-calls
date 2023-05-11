<?php
declare(strict_types = 1);

use Fiction\Pulp\Royale;
use Waldo\Quux\Blade;

// allowed by path
$blade = new Blade();
$blade->andSorcery();
$blade->server();

// allowed by path
Royale::withCheese();
Royale::leBigMac();

// allowed because it's a built-in function
time();
