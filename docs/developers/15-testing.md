<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/testing
source-id: 3531
modified: 2015-08-07
imported: 2026-09-09
-->
# Testing

Hubzero has a working PHPUnit suite across the framework libraries, seven
components and two plugins, plus two custom linters that catch faults nothing
else does. This page says what is there, how to run it, what is worth adding
to it, and how to run one test instead of all of them.

A full run on 2.4-main passes:

```
Tests: 855, Assertions: 2905, PHPUnit Deprecations: 83, Skipped: 6.
```

The deprecations come from PHPUnit 11 warning about older test syntax, not
from failures. Six tests skip themselves when what they need is absent.

## What is worth testing

The suite is not a safety net over the whole CMS and pretending otherwise
wastes your afternoon. It is 855 tests over a codebase of several thousand
files, and it splits sharply:

| Suite | Tests | What it covers |
|---|---|---|
| `libraries` | 694 | The framework: the query builder, `Relational`, the config registry and its processors, the container, facades, cache, encryption, utility string and array helpers |
| `components` | 150 | Seven components, and mostly their pure helpers — search query building, a migration's column statements, a publication bundle builder, SAML metadata |
| `plugins` | 11 | Two plugins, one method each |

Everything in that table has the same shape: **a class you can construct with
`new`, hand some input, and check the output of.** That is what this suite is
good at, and it is what to write a test for.

What is not covered, and what a test here cannot easily reach:

- **Controllers.** Nothing tests a site, administrator or API controller
  end to end. A controller reaches for `Request`, `User`, `Config`, the
  document and the session, and the test bootstrap gives you a container with
  enough in it to load the file, not enough to serve a request.
- **Views.** No layout is rendered by any test.
- **Permissions.** No test exercises `User::authorise()` or an access level.
  A change to a `WHERE` clause that widens who can read a row will not be
  caught here; say what it now allows in the pull request instead.
- **MySQL.** `Hubzero\Test\Database` runs against SQLite, so a test proves
  the query builder produced something SQLite accepted. It does not prove
  MySQL accepts it, and it does not exercise a migration.

So the practical rule for a new extension — say a component that books time
on a lab instrument — is: put the logic that decides something in a class of
its own and test that. The overlap rule the booking has to enforce, the
policy that says who may cancel, the parser for the reservation code: those
are testable, they are where the bugs will be, and testing them costs you
nothing in fixtures. The controller that calls them is checked by using it.

## What exists

| Tool | Where | What it does |
|---|---|---|
| PHPUnit 11.5 | `core/vendor/bin/phpunit` | The test runner |
| `core/phpunit.xml.dist` | | The shipped configuration: three suites |
| `muse test` | `core/bin/muse` | Lists and runs one extension's tests |
| `tools/lint/missing-facade-imports.php` | | Finds unqualified facade calls in namespaced files |
| `tools/lint/undefined-language-keys.php` | | Finds language keys nothing defines |
| `core/bin/php_tests.sh` | | PSR-12 style plus a syntax check, over a list of files |
| `.github/workflows/php-lint.yml` | | CI: `php -l` over `core` and `app`, the facade linter, and the language-key ceiling |
| `.github/workflows/tests.yml` | | CI: the PHPUnit suite |

Tests are named `*Test.php` and live in a `Tests` or `tests` directory
inside the thing they test:

```
core/libraries/Hubzero/Database/Tests/QueryTest.php
core/libraries/Hubzero/Config/Tests/RegistryTest.php
core/components/com_blog/tests/EntryTest.php
core/components/com_search/tests/boostQueryHelperTest.php
core/plugins/user/hubzero/tests/HubzeroIsThirdPartyPlaceholderTest.php
```

## Running them

Everything, through the shipped configuration:

```bash
cd core
vendor/bin/phpunit -c phpunit.xml.dist
```

Or one suite at a time — `libraries`, `components`, `plugins`:

```bash
vendor/bin/phpunit -c phpunit.xml.dist --testsuite libraries
```

The `libraries` suite covers `libraries/Hubzero` and excludes three
production classes that happen to be named `Test.php`. `components` globs
`components/*/tests` and `components/*/helpers/tests`; `plugins` globs
`plugins/*/*/tests`.

> **Note:** The `components/*/helpers/tests` glob matches exactly one
> directory, `com_resources/helpers/tests`, and collects nothing from it. The
> two files there implement `Hubzero\Content\Auditor\Test`, the content
> auditor's interface — the same word, a different thing, and the reason the
> `libraries` suite has to exclude three production files named `Test.php` by
> name. Do not read the glob as evidence that a `helpers/tests` directory is
> a convention here; put a component's tests in `components/com_x/tests`.

### Running one test

A full run takes about half a minute, which is short enough to sit through
and long enough that you will stop doing it while you iterate. Two ways to
narrow it.

By file — pass the path after the configuration:

```bash
cd core
vendor/bin/phpunit -c phpunit.xml.dist libraries/Hubzero/Utility/Tests/InflectorTest.php
```

By name — `--filter` takes a method name, a class name, or a regular
expression matching either:

```bash
vendor/bin/phpunit -c phpunit.xml.dist --filter testBasicFetch
vendor/bin/phpunit -c phpunit.xml.dist --filter QueryTest
```

Run both from `core`. PHPUnit finds `phpunit.xml.dist` there without being
told, so `-c` is belt and braces — but only there. Run the same command from
the installation root and PHPUnit picks up a root `phpunit.xml` if you have
one, which is a different configuration with a different bootstrap and
different suite names. That filename is in `.gitignore`, so a root
`phpunit.xml` is yours, not the project's, and what it does is not what CI
does.

### Through muse

[Muse](12-muse.md) wraps the runner for one extension at a time.
`muse test show` lists what can be run:

```
$ php core/bin/muse test show
lib_base
lib_cache
lib_config
lib_database
…
core:com_blog
core:com_courses
core:com_groups
core:plg_authentication_orcid
core:plg_user_hubzero
```

Names are the extension with its prefix — `com_`, `mod_`, `plg_{group}_`,
`tpl_`, or `lib_` for a framework subsystem — with `core:` or `app:` in front
of everything but a library, because the same extension can exist in both
trees.

```bash
php core/bin/muse test run lib_config
```

`run` requires an extension; there is no way to run everything through muse.
Use PHPUnit directly for that.

> **Note:** `muse test run` invokes PHPUnit with
> `--bootstrap core/bootstrap/phpunit-bootstrap.php` and no configuration
> file of its own, so PHPUnit picks up whatever `phpunit.xml` it finds in the
> working directory. Run it from the installation root.

## Writing a test

Two base classes, both real PHPUnit 11 test cases.

### Basic

[`Hubzero\Test\Basic`](../../core/libraries/Hubzero/Test/Basic.php) extends
`PHPUnit\Framework\TestCase` and adds nothing. If your test needs no
database, extend it — or extend `TestCase` directly — and follow the
[PHPUnit documentation](https://docs.phpunit.de/en/11.5/).

### Database

[`Hubzero\Test\Database`](../../core/libraries/Hubzero/Test/Database.php) is
for tests that need a driver. It gives you a real, throwaway SQLite database
rather than a connection to anyone's development server:

```php
public function testBasicFetch()
{
    $dbo   = $this->getMockDriver();
    $query = new Query($dbo);

    $rows = $query->select('*')
                  ->from('users')
                  ->whereEquals('id', '1')
                  ->fetch();

    $this->assertCount(1, $rows, 'Query should have returned one result');
}
```

`getMockDriver()` returns a fully functioning driver over a SQLite file. Two
fixtures back it, in a `Fixtures` directory beside the test:

```
Tests/Fixtures/test.sqlite3
Tests/Fixtures/seed.xml
```

`test.sqlite3` supplies the schema; `seed.xml` supplies the rows, reloaded
for each test class. Override `$fixture` and `$seed` on the test class to
use different filenames, or override `getDataSet()` for anything more
involved.

`Hubzero\Test\Database` also bootstraps the facades — it adopts the
container the bootstrap installed rather than replacing it, and registers an
event dispatcher — so a model that calls `Event::trigger()` at file scope
does not fatal.

> **Note:** This class is Hubzero's own, not PHPUnit's. It replaces
> `PHPUnit\DbUnit\TestCase`, which was abandoned years ago. The supporting
> classes are in
> [`Hubzero\Test\Database`](../../core/libraries/Hubzero/Test/Database) —
> `Connection`, `DataSet`, `XmlDataSet`, `Table`.

### Scaffolding

```bash
php core/bin/muse scaffolding create test lib_database --type=database
```

The first argument after `test` is the extension; `--type` is `basic` or
`database`.

## The linters

Two faults are invisible to PHP's own syntax check and to any test that does
not happen to execute the affected line. Both have a linter.

### Missing facade imports

The CMS registers `Route`, `Lang`, `User`, `Config` and the rest as
**root-namespace** aliases. Inside a namespaced file, an unqualified
`Route::url()` resolves to `Current\Namespace\Route` and fatals when the line
runs. The file parses; nothing complains until that branch executes, which
on a rarely used error path can be years.

```bash
php tools/lint/missing-facade-imports.php            # core components, plugins, modules, libraries
php tools/lint/missing-facade-imports.php --fix      # insert the missing `use` statements
```

It reads the file with PHP's tokenizer, so a name in a comment, a string or
a heredoc is not counted. It exits non-zero on a finding, and the PHP lint
workflow runs it on every push. The tree is currently clean; 730 of these
were fixed at once, and this is what keeps them from coming back.

### Undefined language keys

`Lang::txt()` returns its argument unchanged when the key is not found, so a
missing string is not an error — the raw key is printed into the page.

```bash
php tools/lint/undefined-language-keys.php
php tools/lint/undefined-language-keys.php core/components/com_blog
```

A key counts as defined if any `en-GB` file anywhere under `core/` or `app/`
defines it, which is deliberately generous: only a handful of files load per
request, so a key defined in some other extension may still fail at runtime.
Keys built at runtime (`'COM_X_' . strtoupper($type)`) cannot be checked and
are skipped, so a clean run does not prove every string resolves.

It runs in CI against a ceiling rather than against zero. 444 keys are
undefined today, so `--max=444` in the workflow lets the check pass while
refusing to let the number grow. Define the keys your change asks for, and
when you have fixed some, lower the ceiling so the count cannot creep back
up.

## Continuous integration

Three workflows run on GitHub:

- **`php-lint.yml`** — on every push to `2.4-main` and on pull requests
  touching any `.php` file. It runs `php -l` over every PHP file in `core`
  and `app` outside `vendor`, then the facade linter, then the language-key
  linter against its ceiling.
- **`tests.yml`** — the PHPUnit suite, on the same events, for changes under
  `core`. It installs the Composer dependencies, caching them against
  `composer.lock`, and runs `vendor/bin/phpunit -c phpunit.xml.dist`.
- **`pages.yml`** — builds this documentation, runs the builder's own Python
  tests, regenerates the references and fails if the committed copy is
  stale, and checks every internal link.

A third, `dev-push.yml`, deploys to a Purdue development host and is
disabled (`if: false`).

Run the suite before sending a change anyway; CI is the backstop, not the
first line.

## What the old page claimed that is not true

This page was imported from help.hubzero.org and described the 2015 state.
For the record:

- Tests extend `PHPUnit\Framework\TestCase`, not `PHPUnit_Framework_TestCase`.
  PHPUnit here is 11.5, not 4.6.
- The "tests are only supported in individual extensions with 2.1.10+" note
  is long spent. Extension tests work and several ship.
- `muse test show` output has changed; the list above is the real one.

Two loose ends worth knowing about:

- `tests/Unit` and `tests/Feature` in the installation root are **empty**,
  and no configuration file refers to them. They are a stub of a layout that
  was never adopted. Do not put tests there.
- A `phpunit.xml` in the root, if you have one, is yours: the filename is in
  `.gitignore`. `core/phpunit.xml.dist` is the shipped configuration, and it
  is the one to change if the change should reach other people.
