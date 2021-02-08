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

If you want to disable program execution functions (`exec()`, `shell_exec()` & friends), include `disallowed-execution-calls.neon`:

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

### Custom rules

There are four different disallowed types (and configuration keys) that can be disallowed:

1. `disallowedMethodCalls` - for detecting `$object->method()` calls
2. `disallowedStaticCalls` - for static calls `Class::method()`
3. `disallowedFunctionCalls` - for functions like `function()`
4. `disallowedConstants` - for constants like `DateTime::ISO8601` or `DATE_ISO8601`

Use them to add rules to your `phpstan.neon` config file. I like to use a separate file (`disallowed-calls.neon`) for these which I'll include later on in the main `phpstan.neon` config file. Here's an example, update to your needs:

```neon
parameters:
    disallowedMethodCalls:
        -
            method: 'PotentiallyDangerous\Logger::log()'
            message: 'use our own logger instead'
        -
            method: 'Redis::connect()'
            message: 'use our own Redis instead'

    disallowedStaticCalls:
        -
            method: 'PotentiallyDangerous\Debugger::log()'
            message: 'use our own logger instead'

    disallowedFunctionCalls:
        -
            function: 'var_dump()'
            message: 'use logger instead'
        -
            function: 'print_r()'
            message: 'use logger instead'

    disallowedConstants:
        -
            constant: 'DATE_ISO8601'
            message: 'use DATE_ATOM instead'
        -
            constant: 'DateTimeInterface::ISO8601'
            message: 'use DateTimeInterface::ATOM instead'
```

The `message` key is optional. Functions and methods can be specified with or without `()`.

Use wildcard (`*`) to ignore all functions or methods starting with a prefix, for example:
```neon
parameters:
    disallowedFunctionCalls:
        -
            function: 'pcntl_*()'
```
The wildcard must be the rightmost character of the function or method name, optionally followed by `()`.

You can treat `eval()` as a function (although it's a language construct) and disallow it in `disallowedFunctionCalls`.

To disallow naive object creation (`new ClassName()` or `new $classname`), disallow `NameSpace\ClassName::__construct` in `disallowedMethodCalls`. Works even when there's no constructor defined in that class.

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

The paths in `allowIn` are relative to the config file location and support [fnmatch()](https://www.php.net/function.fnmatch) patterns.

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

## See also
There's a similar project with a slightly different configuration, created almost at the same time (just a few days difference): [PHPStan Banned Code](https://github.com/ekino/phpstan-banned-code).
