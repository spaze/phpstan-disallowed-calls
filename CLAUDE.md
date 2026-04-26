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

`extension.neon` is the entry point wiring all rules together. It also defines the NEON schema for all config options — type declarations like `string()`, `int()`, `bool()` are enforced by NEON at config parse time, before any PHP runs. This means defensive runtime checks for wrong-typed config values (e.g. an array where a string is expected) are unnecessary when using the extension normally through PHPStan. Each feature has its own documentation file in `docs/` — new features get their own doc or extend an existing one. Read the relevant `docs/` file when helping a user write or debug a config — it documents all available keys and their semantics.

PHPStan ships as a phar (`vendor/phpstan/phpstan/phpstan.phar`) which contains more classes than what's visible in `vendor/phpstan/`. When looking for PHPStan internals or bundled library utilities, search the phar contents before concluding something doesn't exist.

`disallow*` and `allowExcept*` config keys are equally valid alternatives for the same behaviour, handled in `AllowedConfigFactory`. Config keys follow a `*Anywhere` / `*InAllowed` naming convention — bare names without these suffixes (e.g. `allowExceptParams`, `disallowParams`) are legacy and kept for backwards compatibility; new keys should use the suffixed form (`allowExceptParamsAnywhere` and `disallowParamsAnywhere` are equally valid). Each new alias should have its own test — the schema and factory both use plain strings so a typo wouldn't be caught by PHPStan.

## Commit message style

No "fix" in titles. The extended message explains why, not how.

## PR description style

No bullet points, no `## Summary` header. Write in sentences. No test plan section.
