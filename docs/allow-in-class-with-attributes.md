## Allow in class with given attributes

It is possible to allow a previously disallowed function, method call or an attribute usage when done in a class with specified attributes.
You can use the `allowInClassWithAttributes` configuration option.

For example, if you'd have a configuration like this:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            allowInClassWithAttributes:
                - MyAttribute
```

Then the `log()` call would be allowed in a class that would look like this, note the attribute added on the class:

```php
#[MyAttribute]
class Foo
{
    public function bar(): void
    {
        $this->dangerousLogger->log('something');
    }
}
```

On the other hand, if you need to disallow a method call, a function call, or an attribute usage only when present in a method from a class with a given attribute,
use `allowExceptInClassWithAttributes` (or the `disallowInClassWithAttributes` alias):

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            allowExceptInClassWithAttributes:
                - SomeAttribute
```

The `log()` method call would be allowed in the following class:

```php
class Foo
{
    public function bar(): void
    {
        $this->dangerousLogger->log('something');
    }
}
```

It would be disallowed in this class and in this class only because it has the `SomeAttribute` attribute:

```php
#[SomeAttribute]
class Foo
{
    public function bar(): void
    {
        $this->dangerousLogger->log('something');
    }
}
```

The attribute names in the _allow_ directives support [fnmatch()](https://www.php.net/function.fnmatch) patterns.
