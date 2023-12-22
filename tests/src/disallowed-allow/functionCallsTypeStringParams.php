<?php
declare(strict_types = 1);

use function Foo\Bar\Waldo\config;

config([]); // disallowed
config(['foo' => 'bar']); // allowed
config(['foo' => 'bar'], ['what' => 'ever']); // allowed by path but param #1 must match
\Foo\Bar\Waldo\foo('foo'); // disallowed param value
\Foo\Bar\Waldo\foo('bar'); // allowed by path, allowed param value
\Foo\Bar\Waldo\bar('bar'); // allowed by path
\Foo\Bar\Waldo\bar('baz'); // allowed by path
\Foo\Bar\Waldo\baz('CaSe'); // allowed param
\Foo\Bar\Waldo\baz('iNsEnSiTiVe'); // allowed by path
\Foo\Bar\Waldo\arrayParam1([]); // allowed by path
\Foo\Bar\Waldo\arrayParam1(['foo']); // allowed by path
\Foo\Bar\Waldo\arrayParam2([]); // allowed by path
\Foo\Bar\Waldo\arrayParam2(['bar']); // allowed by path
\Foo\Bar\Waldo\mocky('moc'); // allowed param
\Foo\Bar\Waldo\mocky('ky'); // allowed param
\Foo\Bar\Waldo\mocky('mocky'); // allowed param
\Foo\Bar\Waldo\intParam1(1 | 2); // allowed flag
\Foo\Bar\Waldo\intParam1(1 | 4); // not an allowed flag
\Foo\Bar\Waldo\intParam2(1 | 2); // allowed flag
\Foo\Bar\Waldo\intParam2(1 | 4); // allowed by path
\Foo\Bar\Waldo\intParam3(1 | 2); // disallowed flag
\Foo\Bar\Waldo\intParam3(1 | 4); // not a disallowed flag
\Foo\Bar\Waldo\intParam4(1 | 2); // allowed by path
\Foo\Bar\Waldo\intParam4(8 | 16); // allowed by path
\Foo\Bar\Waldo\intParam4(1 | 4); // allowed by path
\Foo\Bar\Waldo\mixedParam1(new DateTime()); // disallowed param
\Foo\Bar\Waldo\mixedParam1(new DateTimeImmutable()); // disallowed param
\Foo\Bar\Waldo\mixedParam1(new Exception); // not a disallowed param
