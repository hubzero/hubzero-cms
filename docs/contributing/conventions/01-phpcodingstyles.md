<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions/phpcodingstyles
-->
# PHP Coding Style

Hubzero writes PHP to [PSR-12](https://www.php-fig.org/psr/psr-12/) with three
deliberate departures: indentation is a tab, the opening brace of a control
structure goes on its own line, and a leading underscore on a non-public member
is still allowed. Everything else PSR-12 says holds.

> **Note:** The repository ships no phpcs ruleset. PSR-12 is a description of
> the house style, not a gate the build enforces. What continuous integration
> checks is in [What the build checks](#what-the-build-checks) below.

## The three departures

### Indent with tabs

One tab per level. PSR-12 asks for four spaces; core does not use them. Of the
2,870 PHP files under `core/components`, 2,809 indent with tabs and 32 with
spaces.

Newer framework files written from scratch — `Hubzero\Base\ClassLoader`, for
one — use four spaces. Match the file you are editing. Never mix the two in a
single file, and never reindent a file you are otherwise only patching: it
buries the change in a diff nobody can read.

### Braces on their own line

The opening brace goes on the line below, for classes, methods, functions
**and** control structures. PSR-12 puts a control structure's brace on the same
line; core does not.

```php
if ($filters['month'] > 12)
{
	$filters['month'] = 0;
}

foreach ($rows as $row)
{
	$row->save();
}
```

### A leading underscore is allowed

PSR-12 says an underscore prefix has no meaning. Core uses one on protected and
private members throughout — 887 methods and 718 properties under
`core/components` and `core/libraries`. Do not add the prefix to new code;
do not strip it from existing code either, because the name is part of the
class's contract with its subclasses.

## Files

- Open with `<?php`. Short tags are never allowed, and `<?=` appears nowhere in
  core.
- Omit the closing `?>` in a file that is only PHP; it is not required, and
  leaving it off keeps trailing whitespace out of the response. A file that
  ends in markup keeps its final `?>` because it has to.
- End the file with a single newline, and use Unix line endings (LF, `0x0A`).
  Never CR or CRLF.
- One class per file.

Every PHP file opens with the same four-line docblock:

<!--include: core/components/com_blog/site/controllers/entries.php:1-6-->

`@package` is `hubzero-cms` for extensions and `framework` for files under
`core/libraries/Hubzero`. `@license` is always MIT. A new file gets

```php
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
```

and a file modified from core keeps the earliest year already there:
`Copyright © 2015-2026 …`. The long LGPL preamble that older documentation
showed is gone; no file in the tree carries it.

### The direct-access gate

A file that is reached by including it — a view template, a plugin entry point,
a helper the router pulls in — declares the gate immediately after the header:

```php
// No direct access
defined('_HZEXEC_') or die;
```

3,389 files do. A file that is only ever reached through the autoloader does
not need it; the autoloader will not run arbitrary code on request.

### Importing global facades

The CMS registers `Route`, `App`, `User`, `Lang`, `Event`, `Config`, `Request`,
`Notify` and the rest as root-namespace aliases. Inside a namespaced file an
unqualified `Route::url()` resolves to `Current\Namespace\Route`, not the alias,
and fatals when the line runs. Import every facade you call:

```php
namespace Components\Blog\Site\Controllers;

use Hubzero\Component\SiteController;
use Request;
use Route;
use Lang;
use User;
```

A leading backslash (`\Route::url()`) works too, but the `use` list is the house
form. This is the one style rule with a linter behind it; see below.

## Lines

The soft limit is 120 characters. There is no hard limit: a longer line is a
warning, never an error, and readability wins over the count. Split a long call
by pulling its arguments into variables first.

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

Concatenate with `.`, a space on each side. When a concatenated expression runs
long, break it and align the `.` under the `=`:

```php
$sql = "SELECT `id`, `name` FROM `users` "
     . "WHERE `name` = 'Jim' "
     . "ORDER BY `name` ASC";
```

## Arrays

`array()` is the prevailing form in core — 11,377 uses under
`core/components` — and short `[]` syntax is accepted in new code. Pick one per
file.

A single space after each comma. A multi-line array indents its items one level
and puts the closing paren on its own line at the level of the declaration:

```php
$filters = array(
	'year'       => Request::getInt('year', 0),
	'month'      => Request::getInt('month', 0),
	'scope'      => $this->config->get('show_from', 'site'),
	'authorized' => false,
	'state'      => 1,
	'access'     => User::getAuthorisedViewLevels()
);
```

Aligning the `=>` operators is house style, as above. A trailing comma on the
last item is allowed but core generally omits it.

## Classes

Name classes as [PHP Naming Conventions](02-phpnamingconventions.md) describes.
`extends` and `implements` stay on the class line:

```php
class Entries extends SiteController
{
}
```

Declare visibility on every property and method. `var` is not used. Properties
come before methods.

## Functions and methods

No space between the name and the opening parenthesis. Arguments separated by
`, `. Arguments with defaults go last, and the default gets a space on each
side of the `=` — `$limit = 25`, not `$limit=25`. Both forms are in core; the
spaced one is more common and is what PSR-12 asks for. Do not wrap a return
value in parentheses — `return $this->bar;`, not `return($this->bar);`.

Call-time pass-by-reference (`foo(&$bar)` at the call site) is a PHP fatal and
is never used. Declare the reference in the signature instead:

```php
public function onContentPrepare($context, &$article, &$params)
{
}
```

## Control structures

One space after the keyword, none inside the parentheses. Braces are always
required, even for a single statement. `elseif`, not `else if` — both appear in
core, and `elseif` is the rule for new code.

Break a long condition before the operator, and indent the continuation so the
operator hangs one column left of the first clause:

```php
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}
```

Put the operator at the start of the continuation line, not the end of the one
before, and do not mix the two in one condition.

A `switch` indents its `case` one level and the case body one more. Give it a
`default`. Where a non-empty case falls through deliberately, say so with a
`// no break` comment, so the next reader does not read it as a bug:

```php
switch ($state)
{
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
[Database Schema](04-databaseschema.md).

```php
$this->db->setQuery("SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote($title));
```

Quote every value through `$db->quote()` or bind it. Prefer the query builder
over hand-written SQL in new code; see
[Database](../../developers/06-database/README.md).

## Checking your work

The tools live under `core/vendor/bin`, installed by `php bin/composer install`
run from the `core` directory. The lint and test commands expect that working
directory too:

```bash
cd core
vendor/bin/parallel-lint --exclude vendor .
vendor/bin/phpunit -c phpunit.xml.dist
```

The facade check runs from the repository root:

```bash
php tools/lint/missing-facade-imports.php
```

It takes `--fix` to insert the missing `use` statements, and any path to narrow
the scan; with no path it scans `core/components`, `core/plugins`,
`core/modules` and `core/libraries/Hubzero`.

phpcs is installed as a development dependency but the repository commits no
ruleset, so running plain PSR-12 against core reports mostly the three
departures above: one ordinary controller draws 772 errors, nearly all of them
tab indentation and control-structure braces. Excluding the sniffs that see only
those departures makes the result readable — the same file drops to a few dozen
findings worth reading:

```bash
house_style=Generic.WhiteSpace.DisallowTabIndent
house_style=$house_style,Generic.WhiteSpace.ScopeIndent
house_style=$house_style,Squiz.WhiteSpace.ScopeClosingBrace
house_style=$house_style,Squiz.ControlStructures.ControlSignature
house_style=$house_style,PSR12.ControlStructures.ControlStructureSpacing
house_style=$house_style,PSR2.Methods.MethodDeclaration
house_style=$house_style,Squiz.Functions.MultiLineFunctionDeclaration

core/vendor/bin/phpcs --standard=PSR12 --exclude=$house_style path/to/changed/file.php
```

Run it on the files you changed, not on the tree.

## What the build checks

Two GitHub Actions workflows run on every pull request.

| Workflow | Checks |
|---|---|
| [`php-lint.yml`](../../../.github/workflows/php-lint.yml) | `php -l` over every `*.php` under `core` and `app` outside `vendor`, then `tools/lint/missing-facade-imports.php` |
| [`pages.yml`](../../../.github/workflows/pages.yml) | Builds the documentation, regenerates `docs/reference`, checks every internal link, and fails if the committed `gh-pages/public` is stale |

Neither runs phpcs. A style problem is caught in review, not by the build; a
missing facade import and a syntax error are caught by the build.
