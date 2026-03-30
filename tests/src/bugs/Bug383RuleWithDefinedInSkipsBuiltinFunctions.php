<?php
declare(strict_types = 1);

namespace {

	function config()
	{
	}

	// defined outside definedIn path, should be allowed
	config();

	// defined in definedIn path, disallowed
	__();
	\MyNamespace\__();
	\Foo\Bar\Waldo\foo('bar');
	\Foo\Bar\Waldo\config('baz');

	// built-in functions, definitely not defined in definedIn path, should not be disallowed
	$answer = sprintf('%d', 42);
	iterator_to_array(new ArrayIterator([]));
	$length = strlen('42');

}

namespace Foo {

	function config2()
	{
	}

	// defined outside definedIn path, should be allowed
	config2();

	// defined in definedIn path, disallowed
	__();
	\MyNamespace\__();
	\Foo\Bar\Waldo\foo('bar');
	\Foo\Bar\Waldo\config('baz');

	// built-in functions, definitely not defined in definedIn path, should not be disallowed
	$answer = sprintf('%d', 42);
	iterator_to_array(new \ArrayIterator([]));
	$length = strlen('42');

}
