<?php
declare(strict_types = 1);

// disallowed with different messages
\Foo\Bar\Waldo\config();
\Foo\Bar\Waldo\config('foo');
\Foo\Bar\Waldo\config('foo', 'bar');
\Foo\Bar\Waldo\bar();
\Foo\Bar\Waldo\bar('foo');
\Foo\Bar\Waldo\bar('foo', 'bar');
