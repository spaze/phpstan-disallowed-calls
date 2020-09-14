<?php
declare(strict_types = 1);

use Fiction\Pulp;

// allowed by path
\Fiction\Pulp\Royale::withCheese();
Pulp\Royale::withCheese();
Pulp\Royale::withBadCheese();

// allowed by path and only with these params
Pulp\Royale::withoutCheese(1, 2, 3);
$a = 3;
Pulp\Royale::withoutCheese(1, 2, $a);

// disallowed call, allowed by path but params don't match allowed
$a = 5;
Pulp\Royale::withoutCheese(1, 2, $a);

// allowed by path or with these params
Pulp\Royale::withoutCheese(1, 2, 4);

// not a disallowed method
Pulp\Royale::leBigMac();
Pulp\Royale::withMayo();

// parent method allowed by path
Inheritance\Sub::woofer();

// trait method allowed by path
Traits\TestClass::z();
Traits\AnotherTestClass::zz();
