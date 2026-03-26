<?php
declare(strict_types = 1);

function laravel_config_helper()
{
}

// defined outside definedIn path, should be allowed
laravel_config_helper();

// defined in definedIn path, disallowed
__();
MyNamespace\__();
\Foo\Bar\Waldo\foo('bar');
\Foo\Bar\Waldo\config('baz');

// built-in functions, definitely not defined in definedIn path, should not be disallowed
$answer = sprintf('%d', 42);
iterator_to_array(new ArrayIterator([]));
$length = strlen('42');
