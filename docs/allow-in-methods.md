## Allow in methods or functions

To allow a previously disallowed method or function only when called from a different method or function in any file, use `allowInFunctions` (or `allowInMethods` alias):

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            message: 'use our own logger instead'
            allowInMethods:
                - Foo\Bar\Baz::method()
```

And vice versa, if you need to disallow a method or a function call only when done from a particular method or function, use `allowExceptInFunctions` (with aliases `allowExceptInMethods`, `disallowInFunctions`, `disallowInMethods`):

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'Controller::redirect()'
            message: 'redirect in startup() instead'
            allowExceptInMethods:
                - Controller\Foo\Bar\*::__construct()
```

The function or method names support [fnmatch()](https://www.php.net/function.fnmatch) patterns.
