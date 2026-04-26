<?php
declare(strict_types = 1);

\Foo\Bar\Waldo\classPatternAllowed(new DateTime()); // allowed param
\Foo\Bar\Waldo\classPatternAllowed(new DateTimeImmutable()); // allowed param
\Foo\Bar\Waldo\classPatternAllowed(new Exception()); // disallowed param
\Foo\Bar\Waldo\classPatternDisallowed(new DateTime()); // disallowed param
\Foo\Bar\Waldo\classPatternDisallowed(new DateTimeImmutable()); // disallowed param
\Foo\Bar\Waldo\classPatternDisallowed(new Exception()); // allowed param
\Foo\Bar\Waldo\classPatternAllowed(42); // disallowed param (non-object never matches a class pattern)
\Foo\Bar\Waldo\classPatternDisallowed(42); // allowed param (non-object never matches a class pattern)
\Foo\Bar\Waldo\classPatternAllowed(param: new DateTime()); // allowed param (named argument)
\Foo\Bar\Waldo\classPatternAllowed(param: new Exception()); // disallowed param (named argument)
\Foo\Bar\Waldo\classPatternPrecedence(new DateTime()); // allowed param (classPattern matches, typeString ignored)
\Foo\Bar\Waldo\classPatternPrecedence(new Exception()); // disallowed param (classPattern doesn't match, typeString ignored)
