# Disallowed calls for PHPStan
[PHPStan](https://github.com/phpstan/phpstan) rules to detect disallowed calls, without running the code.

[![Build Status](https://travis-ci.org/spaze/phpstan-disallowed-calls.svg?branch=master)](https://travis-ci.org/spaze/phpstan-disallowed-calls)

There are some functions and methods which should not be used in production code. One good example is `var_dump()`,
it is often used to quickly debug problems but should be removed before commiting the code. And sometimes it's not.

Another example would be a generic logger. Let's say you're using one of the generic logging libraries but you have your own logger
that will add some more info, or sanitize data, before calling the generic logger. Your code should not call the generic logger directly
but should instead use your custom logger.

This [PHPStan](https://github.com/phpstan/phpstan) extension will detect such usage, if configured. It should be noted that this extension
is not a way to defend against or detect hostile developers, as they can obfuscate the calls for example. This extension is meant to be
another pair of eyes, detecting your own mistakes.

## Installation

Install the extension using [Composer](https://getcomposer.org/):
```
composer require --dev spaze/phpstan-disallowed-calls
```

[PHPStan](https://github.com/phpstan/phpstan), the PHP Static Analysis Tool, is a requirement.

## Configuration

There are three different classes that can be used:

1. `MethodCalls` - for detecting `$object->method()` calls
2. `StaticCalls` - for static calls `Class::method()`
3. `FunctionCalls` - for functions like `function()`

Use them to add rules to your `phpstan.neon` config file. Here's an example, update to your needs:

```
services:
    - Spaze\PHPStan\Rules\Disallowed\DisallowedHelper
    -
        class: Spaze\PHPStan\Rules\Disallowed\MethodCalls
        tags:
            - phpstan.rules.rule
        arguments:
            forbiddenCalls:
                -
                    method: 'PotentiallyDangerous\Logger::log()'
                    message: 'use our own logger instead'
                -
                    method: 'Redis::connect()'
                    message: 'use our own Redis instead'

    -
        class: Spaze\PHPStan\Rules\Disallowed\StaticCalls
        tags:
            - phpstan.rules.rule
        arguments:
            forbiddenCalls:
                -
                    method: 'PotentiallyDangerous\Debugger::log()'
                    message: 'use our own logger instead'

    -
        class: Spaze\PHPStan\Rules\Disallowed\FunctionCalls
        tags:
            - phpstan.rules.rule
        arguments:
            forbiddenCalls:
                -
                    function: 'var_dump()'
                    message: 'use logger instead'
                -
                    function: 'print_r()'
                    message: 'use logger instead'
```

The `message` key is optional. Don't forget to add the `DisallowedHelper` service.

## Example output

```
 ------ --------------------------------------------------------
  Line   libraries/Report/Processor/CertificateTransparency.php
 ------ --------------------------------------------------------
  116    Calling var_dump() is forbidden, use logger instead
 ------ --------------------------------------------------------
```

## Ignore some calls

Sometimes, the method or the function needs to be called once in your code, for example in a custom wrapper. You can use PHPStan's [`ignoreErrors` feature](https://github.com/phpstan/phpstan#ignore-error-messages-with-regular-expressions) to ignore that one call:

```
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

You can also allow some previously disallowed calls using the `allowIn` configuration key, for example:

```
services:
    - Spaze\PHPStan\Rules\Disallowed\DisallowedHelper
    -
        class: Spaze\PHPStan\Rules\Disallowed\MethodCalls
        tags:
            - phpstan.rules.rule
        arguments:
            forbiddenCalls:
                -
                    method: 'PotentiallyDangerous\Logger::log()'
                    message: 'use our own logger instead'
                    allowIn:
                        - path/to/some/file-*.php
                        - tests/*.test.php
```

The paths in `allowIn` are relative to the config file location and support [fnmatch()](https://www.php.net/function.fnmatch) patterns.

## Running tests

If you want to contribute (awesome, thanks!), you should add/run tests for your contributions.
First install dev dependencies by running `composer install`, then run tests with this command:

```
vendor/bin/phpunit -c tests/phpunit.xml tests/
```
