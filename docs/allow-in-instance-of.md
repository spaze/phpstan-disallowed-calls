## Allow in classes, child classes, classes implementing an interface

Use `allowInInstanceOf`, named after the PHP's `instanceof` operator, if you want to allow an item like function or a method call, an attribute, a classname, or a namespace etc. in
- a class of given name
- a class that inherits from a class of given name
- a class that implements given interface

The class names support [fnmatch()](https://www.php.net/fnmatch) patterns. When a wildcard pattern is used, it is matched against the current class name as well as all its parent class and interface names transitively, so the same instanceof semantics apply.

For example, to allow a function call in any class inside the `App\Wrappers` namespace and its subclasses:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'someFunction()'
            allowInInstanceOf:
                - 'App\Wrappers\*'
```

This is useful for example when you want to allow properties or parameters of class `ClassName` in all classes that extend `ParentClass`:

```neon
parameters:
    disallowedClasses:
        -
            class: 'ClassName'
            allowInInstanceOf:
                - 'ParentClass'
```
Another example could be if you'd like to disallow a `function()` in all classes that implement the `MyInterface` interface.
You can use the `allowExceptInInstanceOf` counterpart (or the `disallowInInstanceOf` alias) for that, like this:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'function()'
            disallowInInstanceOf:
                - 'MyInterface'
```

### Combining with parameter conditions

Both `allowInInstanceOf` and `disallowInInstanceOf` can be combined with `allowParamsInAllowed` and `allowExceptParamsInAllowed` to add parameter-based conditions within the class hierarchy scope. See [allow with parameters](allow-with-parameters.md) for details on parameter configuration.

For example, to allow `dispatch()` in classes implementing `HandlerInterface` but only when the first argument is of type `SafeEvent`:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'dispatch()'
            allowInInstanceOf:
                - 'App\Handlers\HandlerInterface'
            allowParamsInAllowed:
                -
                    position: 1
                    name: 'event'
                    typeString: 'App\Events\SafeEvent'
```

To disallow `dispatch()` in `HandlerInterface` classes only when the first argument is of type `DangerousEvent`, and allow it with any other argument:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'dispatch()'
            disallowInInstanceOf:
                - 'App\Handlers\HandlerInterface'
            allowExceptParamsInAllowed:
                -
                    position: 1
                    name: 'event'
                    typeString: 'App\Events\DangerousEvent'
```

The `allowExceptParamsInAllowed` counterpart works with `allowInInstanceOf` too (allowed in hierarchy except when the parameter matches), and `allowParamsInAllowed` works with `disallowInInstanceOf` (disallowed in hierarchy unless the parameter matches).

### Allow in `use` imports
The `allowInInstanceOf` configuration above will also report an error on the line with the import, if present:
```php
use ClassName;
```
To omit the `use` finding, you can add the `allowInUse` line, like this:

```neon
parameters:
    disallowedClasses:
        -
            class: 'ClassName'
            allowInInstanceOf:
                - 'ParentClass'
            allowInUse: true
```
