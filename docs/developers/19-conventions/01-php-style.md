<!--
status: rewritten
reviewed-against: per-cs-3.1 @ a725be275d
reviewed: 2026-11-10
screenshots: none
-->
# PHP Coding Style

Hubzero writes PHP to [PSR-12](https://www.php-fig.org/psr/psr-12/) and its
successor, [PER Coding Style](https://www.php-fig.org/per/coding-style/), with
no house departures. The repository commits the ruleset that checks it,
[`phpcs.xml.dist`](../../../phpcs.xml.dist), and the whole tree passes it:
run phpcs and anything it reports came from your change.

The rules are PSR-12's, with what PER Coding Style 3.1 adds to them: trailing
commas, arrow-function spacing, method chains, arrays and the like. phpcs
checks PSR-12 only, so the PER additions are marked (PER-CS) below where they
come up. This page shows what the
rules look like in core, adds the handful of conventions they leave open, and
says how to check your work.

## Files

- Open with `<?php`. Short tags are never allowed, and `<?=` appears nowhere in
  core.
- Omit the closing `?>` in a file that is only PHP; it is not required, and
  leaving it off keeps trailing whitespace out of the response. A file that
  ends in markup keeps its final `?>` because it has to.
- End the file with a single newline, and use Unix line endings (LF, `0x0A`).
  Never CR or CRLF.
- Indent with four spaces. Never tabs.
- One class per file.

Every PHP file opens with the same docblock:

<!--include: core/components/com_blog/site/controllers/entries.php:1-7-->

`@package` is `hubzero-cms` for extensions and `framework` for files under
`core/libraries/Hubzero`. `@license` is always MIT. A new file gets

```php
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
```

and a file modified from core keeps the copyright line already there. The long
LGPL preamble that older documentation showed is gone; no file in the tree
carries it.

### Side effects

A file either declares symbols — classes, functions, constants — or runs code.
Not both. That is PSR-1's rule, and phpcs enforces it.

A file reached by including it rather than through the autoloader — almost
always a view template — declares the direct-access gate immediately after the
header:

```php
// No direct access
defined('_HZEXEC_') or die;
```

1,855 files do, 1,784 of them under a `tmpl/` directory. phpcs accepts the gate
as the one statement a symbol-declaring file may run. A file reached only
through the autoloader does not need it: the autoloader will not run arbitrary
code on request.

A few files genuinely do both: `core/bootstrap/app.php`, and template overrides
that declare a helper function and then render with it. They open with
`// phpcs:disable PSR1.Files.SideEffects`. Do not add that line to a class file;
move the code that runs into a method instead.

### Importing global facades

The CMS registers `Route`, `App`, `User`, `Lang`, `Event`, `Config`, `Request`,
`Notify` and the rest as root-namespace aliases. Inside a namespaced file an
unqualified `Route::url()` resolves to `Current\Namespace\Route`, not the
alias, and fails when the line runs if that name means something else there.
Import every facade you call:

```php
namespace Components\Blog\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
```

Core imports the facade class itself, `Hubzero\Facades\Route`, in about 3,400
files. Importing the alias (`use Route;`) does the same job and survives in
about 160; either satisfies the linter. See
[Facades](../03-foundation/06-facades.md#importing-a-facade).

## Lines

The soft limit is 120 characters. There is no hard limit: phpcs reports a
longer line as a warning, which the committed ruleset does not show, and
readability wins over the count. Split a long call by pulling its arguments
into variables first.

No trailing whitespace. One statement per line.

## Strings

Single quotes for a literal with no substitution:

```php
$a = 'Example String';
```

Double quotes when the string contains apostrophes or interpolates a variable.
Both interpolation forms are accepted:

```php
$greeting = "Hello $name, welcome back!";
$greeting = "Hello {$name}, welcome back!";
```

`"${name}"` is not permitted; PHP 8.2 deprecates it.

A heredoc or nowdoc starts on the line that uses it, and its lines, the closing
marker among them, are indented once past that line (PER-CS). PHP strips the
marker's indentation from every line, so the value does not change:

```php
$sql = <<<SQL
    SELECT `id` FROM `#__users`
    WHERE `block` = 0
    SQL;
```

Never break a line inside a string literal to shorten it. A line break inside
quotes is part of the value: `'index` + newline + `.php'` is not
`'index.php'`, and a pattern, a URL or a shell command split that way stops
working without any error. Split a long string into pieces joined with `.`
instead, as below.

Concatenate with `.`, a space on each side. When a concatenated expression runs
long, break before the `.` and indent the continuation one level:

```php
$this->setQuery(
    "SELECT TABLE_NAME"
    . " FROM INFORMATION_SCHEMA.TABLES"
    . " WHERE TABLE_TYPE = 'SEQUENCE'"
);
```

## Arrays

Short syntax, `[]`. Core has no `array()` constructor left.

A single space after each comma. A multi-line array has one value per line,
indents them one level, and puts the closing bracket on the line after the last
value, at the level of the declaration (PER-CS):

```php
$filters = [
    'year'       => Request::getInt('year', 0),
    'month'      => Request::getInt('month', 0),
    'scope'      => $this->config->get('show_from', 'site'),
    'authorized' => false,
    'state'      => 1,
    'access'     => User::getAuthorisedViewLevels(),
];
```

Aligning the `=>` operators is allowed and common.

The opening bracket of a multi-line array never sits on a line of its own
(PER-CS 3.1), except where the array is itself one item of a multi-line array.
An array passed as an argument opens on the call's line, and the other
arguments follow its closing bracket; one argument spanning lines does not
split the argument list:

```php
$link = str_replace([
    '{ID}',
    '{NAME}',
], [
    $user->get('id'),
    $user->get('name'),
], $template);
```

In a `foreach` header, assign the array to a variable first: a split condition
has to start on the line after the parenthesis.

## Lists and trailing commas

PER-CS: a list split across lines, with its first item on the line after the
opening bracket, ends with a trailing comma; a list on one line never has one.
That holds for arrays, argument and parameter lists, `match` arms and closure
`use` lists alike:

```php
$rows = $this->db->select(
    'id',
    'title',
);

$filter = function ($row) use (
    $year,
    $month,
) {
    // ...
};

$pair = [$year, $month];
```

A single argument that spans lines, such as a closure or a long string, does
not split the list it is in: `foo(function () {` … `});` takes no comma.

## Classes

Name classes as [PHP Naming Conventions](02-php-naming.md) describes.
`extends` and `implements` stay on the class line, and the opening brace goes
on the line below:

```php
class Entries extends SiteController
{
}
```

Declare visibility on every property, method and class constant. `var` is not
used. Properties come before methods.

## Functions and methods

The opening brace of a function or method goes on the line below its
signature. No space between the name and the opening parenthesis. Arguments
separated by `, `. Arguments with defaults go last, and the default gets a
space on each side of the `=` — `$limit = 25`, not `$limit=25`. Do not wrap a
return value in parentheses — `return $this->bar;`, not `return($this->bar);`.

A closure has a space after `function` and around `use`; an arrow function
has none after `fn` (PER-CS):

```php
$names = array_map(function ($user) use ($format) {
    return sprintf($format, $user->name);
}, $users);

$ids = array_map(fn($user) => $user->id, $users);
```

Call-time pass-by-reference (`foo(&$bar)` at the call site) is a PHP fatal and
is never used. Declare the reference in the signature instead:

```php
public function onContentPrepare($context, &$article, &$params)
{
}
```

## Control structures

One space after the keyword, none inside the parentheses. The opening brace
goes on the same line as the keyword, and the closing brace on its own line.
Braces are always required, even for a single statement. `elseif`, not
`else if`.

```php
if ($filters['month'] > 12) {
    $filters['month'] = 0;
} elseif ($filters['month'] < 0) {
    $filters['month'] = 1;
}

foreach ($rows as $row) {
    $row->save();
}
```

A condition too long for one line puts each clause on a line of its own,
indented one level, with the boolean operator at the start of the line. The
closing parenthesis and the opening brace share the line after:

<!--include: core/components/com_blog/admin/controllers/entries.php:136-141-->

A `switch` indents its `case` one level and the case body one more; the body
starts on the line after the `case` and is never wrapped in braces. Give it a
`default`. Every non-empty case ends with `break`, `return` or the like, the
last one included (PER-CS 3.1). Where a case falls through deliberately, say so
with a `// no break` comment instead, so the next reader does not read it as a
bug:

```php
switch ($state) {
    case 'archived':
        $this->archive();
        // no break

    case 'published':
        $this->publish();
        break;

    default:
        break;
}
```

A closing brace has nothing after it on its line, not even a comment saying
which block it closes (`} // end foreach`); the indentation says that.

## Operators

A binary operator has a space on each side. An expression split across lines
puts the operator at the start of each new line, indented once. A split ternary
takes three lines, never two (PER-CS 3.1):

```php
$label = $row->isPublished()
    ? Lang::txt('JPUBLISHED')
    : Lang::txt('JUNPUBLISHED');
```

## Method chains

A chain split across lines puts its first call on the line after its subject,
each call on a line of its own, indented once (PER-CS):

```php
$rows = $this->db
    ->getQuery()
    ->select('*')
    ->from('#__users')
    ->rows();
```

Property access stays with its variable (`$this->db`), and a chain that starts
with a static call keeps it on the first line: `Entry::all()` and then
`->whereEquals(...)` below it. A call whose arguments span lines indents them
from the call's own line.

## Documentation blocks

Docblocks are phpDocumentor format. Every class gets a short description; every
method gets a description, its parameters, and its return type. Columns are
aligned with spaces, two after each tag:

```php
    /**
     * Generate an alias from the data being saved
     *
     * @param   array   $data  the data being saved
     * @return  string
     */
    public function automaticAlias($data)
```

Add `@throws` for every exception class a method can raise. Do not write
`@access`; the visibility modifier already says it. Do not write `@version` or
`@package_version@`; nothing substitutes them.

## SQL

Keywords uppercase, identifiers lowercase and backquoted. Write the table
prefix as the `#__` placeholder, never a literal `jos_` — see
[Database Schema](04-database-schema.md).

```php
$this->db->setQuery("SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote($title));
```

Quote every value through `$db->quote()` or bind it. Prefer the query builder
over hand-written SQL in new code; see
[Database](../06-database/index.md).

## Checking your work

The tools live under `core/vendor/bin`, installed by `php bin/composer install`
run from the `core` directory. phpcs and PHPStan read their configuration from
the repository root, so run them from there:

```bash
core/vendor/bin/phpcs                    # the whole tree, about half a minute
core/vendor/bin/phpcs path/to/file.php   # just what you changed
core/vendor/bin/phpstan analyse --memory-limit=4G
php core/bin/lint/missing-facade-imports.php
```

The lint and test commands expect `core` as the working directory:

```bash
cd core
vendor/bin/parallel-lint --exclude vendor .
vendor/bin/phpunit -c phpunit.xml.dist
```

| Tool | Configuration | What it catches |
|---|---|---|
| phpcs | [`phpcs.xml.dist`](../../../phpcs.xml.dist) | The PSR-12 rules above, across `core/bootstrap`, `components`, `libraries`, `migrations`, `modules`, `plugins` and `templates`. Test directories are excluded. Warnings are not shown, except that two PSR-12 MUSTs phpcs reports as warnings are raised to errors: an underscore-prefixed property name and a constant without visibility. It does not check the PER-CS additions; PHP-CS-Fixer's `@PER-CS3.0` set checks some of them, and none of the 3.1 additions. |
| phpcbf | the same | Fixes most of what phpcs reports. Run it on the files you changed and read the diff. |
| PHPStan | [`phpstan.neon`](../../../phpstan.neon) | At level 0, over every PHP file outside `core/vendor` (tests, migrations and templates included): classes, functions and methods that do not exist, wrong argument counts, undefined variables, includes that point nowhere. The tree has no findings. |
| `missing-facade-imports.php` | none | A facade called without a `use`. Takes `--fix` to insert the missing imports, and any path to narrow the scan. |

A phpcs finding in a file you touched is yours to fix, including one on a line
you did not change: the tree is clean, so it was not there before. Do not add a
`phpcs:disable` or `phpcs:ignore` to quiet one. Nearly all the tree carries are
for side effects in the files described [above](#side-effects); the rest are
two view templates with long lines and one standalone command-line script.

## What the build checks

Three GitHub Actions workflows run on a pull request, each on the paths it
cares about.

| Workflow | Checks |
|---|---|
| [`php-lint.yml`](../../../.github/workflows/php-lint.yml) | `php -l` over every `*.php` under `core` and `app` outside `vendor`, then `core/bin/lint/missing-facade-imports.php`, then `core/bin/lint/undefined-language-keys.php` against the ceiling declared in the workflow |
| [`tests.yml`](../../../.github/workflows/tests.yml) | The PHPUnit suite, for a change under `core` |
| [`pages.yml`](../../../.github/workflows/pages.yml) | Builds the documentation, regenerates `docs/reference`, checks every internal link, and fails if the committed `gh-pages/public` is stale |

None of them runs phpcs or PHPStan yet. Run both before you open a pull
request; a reviewer will.
