## Allow in methods or functions with given attributes

Similar to [allowing items in methods or functions by name](allow-in-methods.md), you can allow or disallow items like functions and method calls, attributes, namespace and classname usage in methods and functions with given attributes.

You can use `allowInMethodsWithAttributes` (or the `allowInFunctionsWithAttributes` alias) for that:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            allowInMethodsWithAttributes:
                - MyAttribute
```

And vice versa, if you need to disallow an item in a method or a function with given attribute, use `allowExceptInMethodsWithAttributes` (with aliases `allowExceptInFunctionsWithAttributes`, `disallowInMethodsWithAttributes`, `disallowInFunctionsWithAttributes`):

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'Controller::redirect()'
            disallowInFunctionsWithAttributes:
                - YourAttribute
```

The attribute names support [fnmatch()](https://www.php.net/function.fnmatch) patterns. If you specify multiple attributes, the method or the function in which the item should be allowed or disallowed, needs to have just one of them.
