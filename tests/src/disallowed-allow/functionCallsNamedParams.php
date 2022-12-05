<?php
declare(strict_types = 1);

namespace Whatever;

use function Foo\Bar\Waldo\bar;
use function Foo\Bar\Waldo\baz;
use function Foo\Bar\Waldo\foo;
use function Foo\Bar\Waldo\waldo;

// fourth/$path param needed
foo('foo');
foo('foo', 'bar');
foo('foo', value: 'bar');
foo('foo', 'bar', 0, '/');
foo('foo', 'bar', path: '/');

// all allowed, no allowParamsInAllowedAnyValue rules
bar();
bar('name');
bar(name: 'name');
bar('name', 'value');
bar('name', 'value', 123, 'path');
bar('name', 'value', path: 'path');
bar(name: 'name', value: 'value', path: 'path');
bar(path: 'path', name: 'name', value: 'value');

// allowed function if second param = VALUE
baz('name', 'value');
baz('name', 'VALUE');
baz('name', value: 'value');
baz('name', value: 'VALUE');

// allowed function if $value param = VALUE
waldo('name', 'value');
waldo('name', 'VALUE');
waldo('name', value: 'value');
waldo('name', value: 'VALUE');
