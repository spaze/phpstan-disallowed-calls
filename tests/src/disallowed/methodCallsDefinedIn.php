<?php
declare(strict_types = 1);

use Fiction\Pulp\Royale;
use Waldo\Quux\Blade;

// disallowed based on definedIn
$blade = new Blade();
$blade->andSorcery();
$blade->server();

// allowed because these are defined elsewhere
Royale::withCheese();
Royale::leBigMac();

// allowed because it's a built-in function
time();
