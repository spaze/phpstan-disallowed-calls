## Disallowing enums

Similar to disallowing constants, enums have some limitations, because internally, enums are disallowed as constants.

First, disallowing enums doesn't support wildcards. The only use case I could think of would be disallowing all cases, e.g. `Enum::*`, which can be achieved by adding the enum to `disallowedClasses`.

Second, enums have to be specified using two keys: `enum` and `case`:
```neon
parameters:
    disallowedEnums:
        -
            enum: 'Suit'
            case: 'Hearts'
            message: 'use Diamonds instead'
```
This is to prevent the enum case being treated like a real enum by the config parser.

Both pure enums and backed enums are supported.

When an enum is disallowed with `disallowedClasses` or `disallowedNamespaces`, its cases, constants and type declarations are reported as `Enum Suit is forbidden.` with the `disallowed.enum` error identifier.
Other classes are reported as `Class ... is forbidden.` with `disallowed.class`, see [error identifiers](custom-rules.md#error-identifiers).
