<?php
// This is a copy of disallowed-calls.php to test disallowed-but-allowed calls
declare(strict_types = 1);

use function Foo\Bar\waldo;

var_dump('foo', true);
print_r('bar');
\printf('foobar');
var_export('not disallowed');
\Foo\Bar\waldo();
waldo();


\Fiction\Pulp\Royale::withCheese();

use Fiction\Pulp;

Pulp\Royale::withCheese();
Pulp\Royale::leBigMac();
Pulp\Royale::withBadCheese();
Pulp\Royale::withoutCheese(1, 2, 3);
Pulp\Royale::withoutCheese(1, 2, 4);


use Waldo\Quux;

$blade = new Quux\Blade();
$blade->runner();
$blade->server();
$blade->runner(42, true, '909');
$blade->runner(42, true, '808');

print_r('bar bar', true);
print_r('bar bar baz', true, 303);
print_r('bar bar was', false);
