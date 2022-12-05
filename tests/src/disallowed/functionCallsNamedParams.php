<?php
declare(strict_types = 1);

namespace Whatever;

use function Foo\Bar\Waldo\bar;
use function Foo\Bar\Waldo\baz;
use function Foo\Bar\Waldo\foo;
use function Foo\Bar\Waldo\waldo;

// second/$value param needed
foo('foo');
foo('foo', 'bar');
foo('foo', value: 'bar');
foo('foo', 'bar', 0, '/');
foo('foo', 'bar', path: '/');

// first param/$name + second param + $path needed
bar();
bar('name');
bar(name: 'name');
bar('name', 'value');
bar('name', 'value', 123, 'path');
bar('name', 'value', path: 'path');
bar(name: 'name', value: 'value', path: 'path');
bar(path: 'path', name: 'name', value: 'value');

// disallowed function
baz('name', 'value');
baz('name', 'VALUE');
baz('name', value: 'value');
baz('name', value: 'VALUE');

// disallowed function
waldo('name', 'value');
waldo('name', 'VALUE');
waldo('name', value: 'value');
waldo('name', value: 'VALUE');
