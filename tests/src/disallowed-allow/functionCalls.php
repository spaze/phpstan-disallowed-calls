<?php
declare(strict_types = 1);

use function Foo\Bar\waldo;

// allowed by path
var_dump('foo', true);
print_r('bar');
\printf('foobar');
\Foo\Bar\waldo();
waldo();
shell_exec('foo --bar');
exec('bar --foo');

// not disallowed function
var_export('not disallowed');
printfunk();
exif_imagetype('1337.jif');

// allowed by path
print_r('bar bar', true);
print_r('bar bar baz', true, 303);

// allowed by path
print_r('bar bar was', false);
