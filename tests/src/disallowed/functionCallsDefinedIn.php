<?php
declare(strict_types = 1);

// disallowed by function name and functions defined in definedIn path too
\Foo\Bar\Waldo\fred();
\Foo\Bar\Waldo\foo();

// not disallowed, name would match but functions defined not in definedIn path
\Foo\Bar\Waldo\bar();
\Foo\Bar\Waldo\baz();

// disallowed by name, no matter where it's defined
\Foo\Bar\Waldo\quux();
