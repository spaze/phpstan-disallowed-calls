# PHPStan Disallowed Calls

## Running tests

```bash
composer test
```

Runs lint, neon lint, phpcs, phpstan, and phpunit in sequence. PHPStan is at `vendor/phpstan/phpstan/phpstan.phar`.

## Test structure

Rule-analysis tests extend `RuleTestCase`. The rule configuration is passed inline in `getRule()`, not via neon files. `$this->analyse()` takes a fixture file and an array of expected errors as `[message, line]` or `[message, line, tip]` tuples. Some tests load `extension.neon` via `getAdditionalConfigFiles()`.

Test classes live in `tests/Calls/`, `tests/Usages/`, `tests/ControlStructures/` etc. Fixture files (PHP files analysed by the tests) live in `tests/src/`.

## Project structure

`extension.neon` is the entry point wiring all rules together. It also defines the NEON schema for all config options — type declarations like `string()`, `int()`, `bool()` are enforced by NEON at config parse time, before any PHP runs. This means defensive runtime checks for wrong-typed config values (e.g. an array where a string is expected) are unnecessary when using the extension normally through PHPStan. Each feature has its own documentation file in `docs/` — new features get their own doc or extend an existing one.

`disallow*` config keys are generally aliases for their `allowExcept*` counterparts, handled in `AllowedConfigFactory`.

## Commit message style

No "fix" in titles. The extended message explains why, not how.

## PR description style

No bullet points, no `## Summary` header. Write in sentences. No test plan section.
