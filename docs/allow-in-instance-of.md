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
