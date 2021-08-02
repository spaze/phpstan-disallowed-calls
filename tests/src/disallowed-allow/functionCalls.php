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

// a language construct, allowed by path
eval('$foo="bar";');
if (random_int(0, 1) === 1) {
	die('hard');
}
if (random_int(0, 1) === 1) {
	die;
}
if (random_int(0, 1) === 1) {
	exit('through the gift shop');
}
if (random_int(0, 1) === 1) {
	exit;
}
empty($bottle);
echo "hello";
print "hello";

// backtick operator allowed by path
`ls`;

// disallowed value in an otherwise allowed param, allowed by path
hash('md4', 'biiig nope');
hash('md5', 'nope');
hash('Md5', 'nOpE');
hash('sha256', 'oh yeah but not for passwords tho');

// third param needed
setcookie('foo', 'bar');
setcookie('foo', 'bar', 0);
setcookie('foo', 'bar', 0, '/');

// third param needed, any value
header('foo: bar');
header('foo: bar', true);
header('foo: bar', false, 303);
