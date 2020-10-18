<?php
declare(strict_types = 1);

use function Foo\Bar\waldo;

// disallowed
var_dump('foo', true);
print_r('bar');
\printf('foobar');
\Foo\Bar\waldo();
waldo();
shell_exec('foo --bar');
exec('bar --foo');

// not a disallowed function
var_export('not disallowed');
printfunk();
exif_imagetype('1337.jif');

// allowed, param #2 is true
print_r('bar bar', true);
print_r('bar bar baz', true, 303);

// disallowed, param #2 is not true
print_r('bar bar was', false);

// a disallowed language construct
eval('$foo="bar";');
