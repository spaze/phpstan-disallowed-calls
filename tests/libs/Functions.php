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
