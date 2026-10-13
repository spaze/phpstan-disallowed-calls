## Allow in methods or functions with given attributes

Similar to [allowing items in methods or functions by name](allow-in-methods.md), you can allow or disallow items like functions and method calls, attributes, namespace and classname usage etc. in methods and functions with given attributes.

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

`allowInMethodsWithAttributes` and `allowInFunctionsWithAttributes` are aliases for one list - when one config entry sets both keys, only `allowInFunctionsWithAttributes` is used, so put all the attributes in a list under one of the keys.
The same applies to `allowExceptInMethodsWithAttributes` and its aliases.

### Attributes on methods

In case of disallowing attributes and then re-allowing them in methods with attributes, the disallowed attributes can be both _inside_ the method, and _on_ the method.
This means that the following code with the following configuration is also valid, even though `Attribute1` is technically not used _inside_ the method.

```php
class Foo
{

    #[Attribute1]
    #[Attribute2]
    public function bar()
    {
    }

}
```

```neon
parameters:
    disallowedAttributes:
        -
            attribute: Attribute1
            allowInMethodsWithAttributes:
                - Attribute2
```

No error would be reported for `Attribute1` as it is used "in" a method with `Attribute2`.

### Property hooks

Code in a `get` or a `set` property hook is allowed or disallowed by the attributes on the hook itself, not by the attributes on the property.
Like with methods, an attribute placed on a hook is also considered to be used in the hook, so for example a disallowed attribute on a hook can be re-allowed by another attribute on the same hook.

```php
class Foo
{

    private PotentiallyDangerous\Logger $logger;

    public string $bar {
        #[MyAttribute]
        set(string $value) {
            $this->logger->log($value);
            $this->bar = $value;
        }
    }

}
```

With the first configuration above, no error would be reported for the `log()` call, because it's in a hook with `MyAttribute`.
