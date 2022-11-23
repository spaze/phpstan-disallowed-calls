# Disallowed calls for PHPStan
[PHPStan](https://github.com/phpstan/phpstan) rules to detect disallowed calls and more, without running the code.

[![PHP Tests](https://github.com/spaze/phpstan-disallowed-calls/workflows/PHP%20Tests/badge.svg)](https://github.com/spaze/phpstan-disallowed-calls/actions?query=workflow%3A%22PHP+Tests%22)

There are some functions, methods, and constants which should not be used in production code. One good example is `var_dump()`,
it is often used to quickly debug problems but should be removed before commiting the code. And sometimes it's not.

Another example would be a generic logger. Let's say you're using one of the generic logging libraries but you have your own logger
that will add some more info, or sanitize data, before calling the generic logger. Your code should not call the generic logger directly
but should instead use your custom logger.

This [PHPStan](https://github.com/phpstan/phpstan) extension will detect such usage, if configured. It should be noted that this extension
is not a way to defend against or detect hostile developers, as they can obfuscate the calls for example. This extension is meant to be
another pair of eyes, detecting your own mistakes, it doesn't aim to detect-all-the-things.

[Tests](tests) will provide examples what is ***currently*** detected. If it's not covered by tests, it might be, but most probably will not be detected.
`*Test.php` files are the tests, start with those, the analyzed test code is in [src](tests/src), required test classes in [libs](tests/libs).

Feel free to file [issues](https://github.com/spaze/phpstan-disallowed-calls/issues) or create [pull requests](https://github.com/spaze/phpstan-disallowed-calls/pulls) if you need to detect more calls.

## Installation

Install the extension using [Composer](https://getcomposer.org/):
```
composer require --dev spaze/phpstan-disallowed-calls
```

[PHPStan](https://github.com/phpstan/phpstan), the PHP Static Analysis Tool, is a requirement.

If you use [phpstan/extension-installer](https://github.com/phpstan/extension-installer), you are all set and can skip to configuration.

For manual installation, add this to your `phpstan.neon`:

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/extension.neon
```


## Configuration

You can start by including `disallowed-dangerous-calls.neon` in your `phpstan.neon`:

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/disallowed-dangerous-calls.neon
```

`disallowed-dangerous-calls.neon` can also serve as a template when you'd like to extend the configuration to disallow some other functions or methods, copy it and modify to your needs.
You can also allow a previously disallowed dangerous call in a defined path (see below) in your own config by using the same `call` or `method` key.

If you want to disallow program execution functions (`exec()`, `shell_exec()` & friends) including the backtick operator (`` `...` ``, disallowed when `shell_exec()` is disallowed), include `disallowed-execution-calls.neon`:

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/disallowed-execution-calls.neon
```

I'd recommend you include both:

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/disallowed-dangerous-calls.neon
    - vendor/spaze/phpstan-disallowed-calls/disallowed-execution-calls.neon
```

To disallow some insecure or potentially insecure calls (like `md5()`, `sha1()`, `mysql_query()`), include `disallowed-insecure-calls.neon`:

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/disallowed-insecure-calls.neon
```

Some function calls are better when done for example with some parameters set to a defined value ("strict calls"). For example `in_array()` better also check for types to prevent some type juggling bugs. Include `disallowed-loose-calls.neon` to disallow calls without such parameters set ("loose calls").

```neon
includes:
    - vendor/spaze/phpstan-disallowed-calls/disallowed-loose-calls.neon
```

### Custom rules

There are several different types (and configuration keys) that can be disallowed:

1. `disallowedMethodCalls` - for detecting `$object->method()` calls
2. `disallowedStaticCalls` - for static calls `Class::method()`
3. `disallowedFunctionCalls` - for functions like `function()`
4. `disallowedConstants` - for constants like `DATE_ISO8601` or `DateTime::ISO8601` (which needs to be split to `class: DateTime` & `constant: ISO8601` in the configuration, see notes below)
5. `disallowedNamespaces` or `disallowedClasses` - for usages of classes or classes from a namespace
6. `disallowedSuperglobals` - for usages of superglobal variables like `$GLOBALS` or `$_POST`

Use them to add rules to your `phpstan.neon` config file. I like to use a separate file (`disallowed-calls.neon`) for these which I'll include later on in the main `phpstan.neon` config file. Here's an example, update to your needs:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'  # `function` is an alias of `method`
            message: 'use our own logger instead'
            errorTip: 'see https://our-docs.example/logging on how logging should be used'
        -
            method: 'Redis::connect()'
            message: 'use our own Redis instead'
            errorIdentifier: 'redis.connect'

    disallowedStaticCalls:
        -
            method: 'PotentiallyDangerous\Debugger::log()'
            message: 'use our own logger instead'

    disallowedFunctionCalls:
        -
            function: 'var_dump()'  # `method` is an alias of `function`
            message: 'use logger instead'
        -
            function: 'print_r()'
            message: 'use logger instead'

    disallowedConstants:
        -
            constant: 'DATE_ISO8601'
            message: 'use DATE_ATOM instead'
        -
            class: 'DateTimeInterface'
            constant: 'ISO8601'
            message: 'use DateTimeInterface::ATOM instead'

    disallowedNamespaces:  # `disallowedClasses` is an alias of `disallowedNamespaces`
        -
            class: 'Symfony\Component\HttpFoundation\RequestStack'  # `class` is an alias of `namespace`
            message: 'pass Request via controller instead'
            allowIn:
                - tests/*
        -
            namespace: 'Assert\*'  # `namespace` is an alias of `class`
            message: 'use Webmozart\Assert instead'

    disallowedSuperglobals:
        -
            superglobal: '$_GET'
            message: 'use the Request methods instead'
```

The `message` key is optional. Functions and methods can be specified with or without `()`. Omitting `()` is not recommended though to avoid confusing method calls with class constants.

If you want to disallow multiple calls, constants, class constants (same-class only), classes, namespaces or variables that share the same `message` and other config keys, you can use a list or an array to specify them all:
```neon
parameters:
    disallowedFunctionCalls:
        -
            function:
                - 'var_dump()'
                - 'print_r()'
            message: 'use logger instead'

    disallowedConstants:
        -
            class: 'DateTimeInterface'
            constant: ['ISO8601', 'RFC3339', 'W3C']
            message: 'use DateTimeInterface::ATOM instead'
```

The optional `errorTip` key can be used to show an additional message prefixed with 💡 that's rendered below the error message in the analysis result.

The `errorIdentifier` key is optional. It can be used to provide a unique identifier to the PHPStan error.

Use wildcard (`*`) to ignore all functions, methods, classes, namespaces starting with a prefix, for example:
```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'pcntl_*()'
```
The wildcard makes most sense when used as the rightmost character of the function or method name, optionally followed by `()`, but you can use it anywhere for example to disallow all functions that end with `y`: `function: '*y()'`. The matching is powered by [`fnmatch`](https://www.php.net/function.fnmatch) so you can use even multiple wildcards if you wish because w\*y n\*t.

You can treat some language constructs as functions and disallow it in `disallowedFunctionCalls`. Currently detected language constructs are:
- `die()`
- `echo()`
- `empty()`
- `eval()`
- `exit()`
- `print()`

To disallow naive object creation (`new ClassName()` or `new $classname`), disallow `NameSpace\ClassName::__construct` in `disallowedMethodCalls`. Works even when there's no constructor defined in that class.

### Disallowing constants

Constants are a special breed. First, a constant needs to be disallowed on the declaring class. That means, that instead of disallowing `Date::ISO8601` or `DateTimeImmutable::ISO8601`, you need to disallow `DateTimeInterface::ISO8601`.
The reason for this is that one might expect that disallowing e.g. `Date::ISO8601` (disallowing on a "used on" class) would also disallow `DateTimeImmutable::ISO8601`, which unfortunately wouldn't be the case.

Second, disallowing constants doesn't support wildcards. The only real-world use case I could think of is the `Date*::CONSTANT` case and that can be easily solved by disallowing `DateTimeInterface::CONSTANT` already.

Last but not least, class constants have to be specified using two keys: `class` and `constant`:
```neon
parameters:
    disallowedConstants:
        -
            class: 'DateTimeInterface'
            constant: 'ISO8601'
            message: 'use DateTimeInterface::ATOM instead'
```
Using the fully-qualified name would result in the constant being replaced with its actual value. Otherwise, the extension would see `constant: "Y-m-d\TH:i:sO"` instead of `constant: DateTimeInterface::ISO8601` for example.

## Example output

```
 ------ --------------------------------------------------------
  Line   libraries/Report/Processor/CertificateTransparency.php
 ------ --------------------------------------------------------
  116    Calling var_dump() is forbidden, use logger instead
 ------ --------------------------------------------------------
```

## Allow some previously disallowed calls

Sometimes, the method, the function, or the constant needs to be called or used once in your code, for example in a custom wrapper. You can use PHPStan's [`ignoreErrors` feature](https://github.com/phpstan/phpstan#ignore-error-messages-with-regular-expressions) to ignore that one call:

```neon
ignoreErrors:
    -
        message: '#^Calling Redis::connect\(\) is forbidden, use our own Redis instead#'  # Needed for the constructor
        path: application/libraries/Redis/Redis.php
    -
        message: '#^Calling print_r\(\) is forbidden, use logger instead#'  # Used with $return = true
        paths:
            - application/libraries/Tls/Certificate.php
            - application/libraries/Tls/CertificateSigningRequest.php
            - application/libraries/Tls/PublicKey.php
```

You can also allow some previously disallowed calls and usages using the `allowIn` configuration key, for example:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            message: 'use our own logger instead'
            allowIn:
                - path/to/some/file-*.php
                - tests/*.test.php
```

Paths in `allowIn` support [fnmatch()](https://www.php.net/function.fnmatch) patterns.

Relative paths in `allowIn` are resolved based on the current working directory. When running PHPStan from a directory or subdirectory which is not your "root" directory, the paths will probably not work.
Use `allowInRootDir` in that case to specify an absolute root directory for all `allowIn` paths. Absolute paths might change between machines (for example your local development machine and a continous integration machine) but you
can use [`%rootDir%`](https://phpstan.org/config-reference#expanding-paths) to start with PHPStan's root directory (usually `/something/something/vendor/phpstan/phpstan`) and then `..` from there to your "root" directory.

For example when PHPStan is installed in `/home/foo/vendor/phpstan/phpstan` and you're using a configuration like this:
```neon
parameters:
    allowInRootDir: %rootDir%/../../..
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            allowIn:
                - path/to/some/file-*.php
```
then `Logger::log()` will be allowed in `/home/foo/path/to/some/file-bar.php`.

If you need to disallow a methods or a function call, a constant, a namespace, a class, or a superglobal usage only in certain paths, as an inverse of `allowIn`, you can use `allowExceptIn` (or the `disallowIn` alias):
```neon
parameters:
    allowInRootDir: %rootDir%/../../..
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            allowExceptIn:
                - path/to/some/dir/*.php
```
This will disallow `PotentiallyDangerous\Logger::log()` calls in `%rootDir%/../../../path/to/some/dir/*.php`.

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

You can also narrow down the allowed items when called with some parameters (doesn't apply to constants for obvious reasons). For example, you want to disallow calling `print_r()` but want to allow `print_r(..., true)`.
This can be done with optional `allowParamsInAllowed` or `allowParamsAnywhere` configuration keys:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            message: 'use our own logger instead'
            allowIn:
                - path/to/some/file-*.php
                - tests/*.test.php
            allowParamsInAllowed:
                1: 'foo'
                2: true
            allowParamsAnywhere:
                2: true
```

When using `allowParamsInAllowed`, calls will be allowed only when they are in one of the `allowIn` paths, and are called with all parameters listed in `allowParamsInAllowed`.
With `allowParamsAnywhere`, calls are allowed when called with all parameters listed no matter in which file. In the example above, the `log()` method will be disallowed unless called as:
- `log(..., true)` anywhere
- `log('foo', true)` in `another/file.php` or `optional/path/to/log.tests.php`

Use `allowParamsInAllowedAnyValue` and `allowParamsAnywhereAnyValue` if you don't care about the parameter's value but want to make sure the parameter is passed.
Following the previous example:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            message: 'use our own logger instead'
            allowIn:
                - path/to/some/file-*.php
                - tests/*.test.php
            allowParamsInAllowedAnyValue:
                - 2
            allowParamsAnywhereAnyValue:
                - 1
```
means that you should use (`...` means any value):
- `log(...)` anywhere
- `log(..., ...)` in `another/file.php` or `optional/path/to/log.tests.php`

Such configuration only makes sense when both the parameters of `log()` are optional. If they are required, omitting them would result in an error already detected by PHPStan itself.

## Allow calls except when a param has a specified value

Sometimes, it's handy to disallow a function or a method call only when a parameter matches but allow it otherwise. For example the `hash()` function, it's fine using it with algorithm families like SHA-2 & SHA-3 (not for passwords though) but you'd like PHPStan to report when it's used with MD5 like `hash('md5', ...)`.
You can use `allowExceptParams` (or `disallowParams`), `allowExceptCaseInsensitiveParams` (or `disallowCaseInsensitiveParams`), `allowExceptParamsInAllowed` (or `disallowParamsInAllowed`) config options to disallow only some calls:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'hash()'
            allowExceptCaseInsensitiveParams:
            	1: 'md5'
```

This will disallow `hash()` call where the first parameter is `'md5'`. `allowExceptCaseInsensitiveParams` is used because the first parameter of `hash()` is case-insensitive (so you can also use `'MD5'`, or even `'Md5'` & `'mD5'` if you wish).
To disallow only exact matches, use `allowExceptParams`:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'foo()'
            allowExceptParams:
            	2: 'baz'
```
will disallow `foo('bar', 'baz')` but not `foo('bar', 'BAZ')`.

It's also possible to disallow functions and methods previously allowed by path (using `allowIn`) or by function/method name (`allowInMethods`) when they're called with specified parameters, and allow when called with any other parameter. This is done using the `allowExceptParamsInAllowed` config option.

Take this example configuration:

```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'waldo()'
            allowIn:
                - 'views/*'
            allowExceptParamsInAllowed:
                2: 'quux'
```

Calling `waldo()` is disallowed, and allowed back again only when the file is in the `views/` subdirectory **and** `waldo()` is called in the file with a 2nd parameter being the string `quux`.

## Case-(in)sensitivity

Function names, method names, class names, namespaces are matched irrespective of their case (disallowing `print_r` will also find `print_R` calls), while anything else like constants, file names, paths are not.

## Detect disallowed calls without any other PHPStan rules

If you want to use this PHPStan extension without running any other PHPStan rules, you can use `phpstan.neon` config file that looks like this (the `customRulesetUsed: true` and the missing `level` key are the important bits):

```neon
parameters:
    customRulesetUsed: true
includes:
    - vendor/spaze/phpstan-disallowed-calls/extension.neon
    - vendor/spaze/phpstan-disallowed-calls/disallowed-dangerous-calls.neon
    - vendor/spaze/phpstan-disallowed-calls/disallowed-execution-calls.neon
```

## Running tests

If you want to contribute (awesome, thanks!), you should add/run tests for your contributions.
First install dev dependencies by running `composer install`, then run PHPUnit tests with `composer test`, see `scripts` in `composer.json`. Tests are also run on GitHub with Actions on each push.

You can fix coding style issues automatically by running `composer cs-fix`.

## See also
There's a similar project with a slightly different configuration, created almost at the same time (just a few days difference): [PHPStan Banned Code](https://github.com/ekino/phpstan-banned-code).

## Framework or package-specific configurations
- For [Nette Framework](https://github.com/spaze/phpstan-disallowed-calls-nette)
