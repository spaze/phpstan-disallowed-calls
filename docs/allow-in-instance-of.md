## Allow in classes, child classes, classes implementing an interface

Use `allowInInstanceOf`, named after the PHP's `instanceof` operator, if you want to allow an item like function or a method call, an attribute, a classname, or a namespace etc. in
- a class of given name
- a class that inherits from a class of given name
- a class that implements given interface

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
