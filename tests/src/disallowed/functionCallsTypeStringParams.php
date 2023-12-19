<?php
declare(strict_types = 1);

use function Foo\Bar\Waldo\config;

config([]); // disallowed
config(['foo' => 'bar']); // disallowed
config(['foo' => 'BAH'], ['waldo' => 'baz', 'pine' => 'apple', 'orly' => [0, -1]]); // param #2 is allowed anywhere, param #1 is irrelevant
\Foo\Bar\Waldo\foo('foo'); // disallowed
\Foo\Bar\Waldo\foo('bar'); // disallowed
\Foo\Bar\Waldo\bar('bar'); // disallowed param
\Foo\Bar\Waldo\bar('baz'); // allowed param
\Foo\Bar\Waldo\baz('CaSe'); // allowed param
\Foo\Bar\Waldo\baz('iNsEnSiTiVe'); // disallowed case-insensitive value
\Foo\Bar\Waldo\arrayParam1([]); // disallowed param
\Foo\Bar\Waldo\arrayParam1(['foo']); // allowed param
\Foo\Bar\Waldo\arrayParam2([]); // disallowed param
\Foo\Bar\Waldo\arrayParam2(['bar']); // allowed param
\Foo\Bar\Waldo\mocky('moc'); // allowed param
\Foo\Bar\Waldo\mocky('ky'); // allowed param
\Foo\Bar\Waldo\mocky('mocky'); // not allowed param
\Foo\Bar\Waldo\intParam1(1 | 2); // disallowed
\Foo\Bar\Waldo\intParam1(1 | 4); // disallowed
\Foo\Bar\Waldo\intParam2(1 | 2); // allowed flag
\Foo\Bar\Waldo\intParam2(1 | 4); // disallowed
\Foo\Bar\Waldo\intParam3(1 | 2); // disallowed
\Foo\Bar\Waldo\intParam3(1 | 4); // disallowed
\Foo\Bar\Waldo\intParam4(1 | 2); // disallowed flag
\Foo\Bar\Waldo\intParam4(8 | 16); // disallowed flag
\Foo\Bar\Waldo\intParam4(1 | 4); // not a disallowed flag
\Foo\Bar\Waldo\mixedParam1(new DateTime()); // disallowed
\Foo\Bar\Waldo\mixedParam1(new DateTimeImmutable()); // disallowed
\Foo\Bar\Waldo\mixedParam1(new Exception); // disallowed
