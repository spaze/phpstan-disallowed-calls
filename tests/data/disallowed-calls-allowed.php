<?php
// This is a copy of disallowed-calls.php to test disallowed-but-allowed calls
declare(strict_types = 1);

use function Foo\Bar\waldo;

var_dump('foo');
print_r('bar');
\printf('foobar');
var_export('not disallowed');
\Foo\Bar\waldo();
waldo();


\Fiction\Pulp\Royale::withCheese();

use Fiction\Pulp;

Pulp\Royale::withCheese();
Pulp\Royale::leBigMac();


use Waldo\Quux;

$blade = new Quux\Blade();
$blade->runner();
$blade->server();
