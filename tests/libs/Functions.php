<?php
declare(strict_types = 1);

namespace Foo\Bar\Waldo;

function quux(): void
{
	$foo = md5('foo');
	$bar = sha1('bar');
}


function fred(): void
{
	$foo = sha1('foo');
}


function foo(string $name, string $value = '', int $expires = 0, string $path = ''): bool
{
}


function bar(string $name = '', string $value = '', int $expires = 0, string $path = ''): bool
{
}


function baz(string $name = '', string $value = '', int $expires = 0, string $path = ''): bool
{
}


function waldo(string $name = '', string $value = '', int $expires = 0, string $path = ''): bool
{
}


function mocky(string $className): void
{
}


/**
 * @param array|string|null $key
 */
function config($key = null, $default = null)
{
}
