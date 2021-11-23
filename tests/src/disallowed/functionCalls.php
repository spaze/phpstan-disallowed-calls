<?php
declare(strict_types = 1);

use function Foo\Bar\waldo;

// disallowed
var_dump('foo', true);
print_R('bar');
\printf('foobar');
\Foo\Bar\waldo();
waldo(123);
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

// backtick operator disallowed when shell_exec() is disallowed
`ls`;

// disallowed value in an otherwise allowed param
hash('md4', 'biiig nope');
hash('md5', 'nope');
hash('Md5', 'nOpE');
hash('sha1', 'nah');
hash('SHA1', 'NAH');
hash('sha256', 'oh yeah but not for passwords tho');

// second param needed
setcookie('foo', 'bar');
setcookie('foo', 'bar', 0);
setcookie('foo', 'bar', 0, '/');

// second param needed, any value
header('foo: bar');
header('foo: bar', true);
header('foo: bar', false, 303);
