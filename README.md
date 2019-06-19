# phpstan-disallowed-calls
[PHPStan](https://github.com/phpstan/phpstan) rules to detect disallowed calls, *Work in Progress, this package needs more love*

[![Build Status](https://travis-ci.org/spaze/phpstan-disallowed-calls.svg?branch=master)](https://travis-ci.org/spaze/phpstan-disallowed-calls)

# Configuration

Add the following to `phpstan.neon` config file and update to your needs:

```
services:
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
```
