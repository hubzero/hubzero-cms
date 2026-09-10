<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
-->
# Conventions

The rules a change has to follow to land in Hubzero. Most of them exist so that
one person can read code another person wrote; a few of them — the naming
rules especially — exist because the autoloader turns a class name into a file
path and gets it wrong when the name is wrong.

Each chapter describes what the code in this repository actually does, with the
counts to show how consistently, and says where core is not uniform so you know
which way to follow.

## In this section

- [PHP Coding Style](#php-coding-style) — PSR-12, its three house
  departures, file headers, docblocks, and the checks the build runs.
- [PHP Naming Conventions](#php-naming-conventions) — how a class name
  becomes a file path for components, plugins, modules, templates and
  migrations.
- [CSS Coding Style](#css-coding-style) — stylesheets and the LESS sources
  the templates compile from.
- [Database Schema Conventions](#database-schema-conventions) — the `#__` prefix
  placeholder, table and column names, indexes.
- [Commit Messages](#commit-messages) — the subject prefix, the body, and what the
  history on this branch actually looks like.

## See also

- [Contributions](18-contributing.md#contributions) — the process a change goes through.
- [Working on the documentation](18-contributing.md#working-on-the-documentation) — writing these pages.
- [Developers](README.md) — how the pieces the conventions
  govern actually work.

## PHP Coding Style

Hubzero writes PHP to [PSR-12](https://www.php-fig.org/psr/psr-12/) with three
deliberate departures: indentation is a tab, the opening brace of a control
structure goes on its own line, and a leading underscore on a non-public member
is still allowed. Everything else PSR-12 says holds.

> **Note:** The repository ships no phpcs ruleset. PSR-12 is a description of
> the house style, not a gate the build enforces. What continuous integration
> checks is in [What the build checks](#what-the-build-checks) below.

### The three departures

None of the three is a better idea than what PSR-12 asks for. They are older
than PSR-12, they are what the tree is written in, and the cost of changing
them is the point: reformatting 2,800 files rewrites every line of every file,
which destroys `git blame`, turns every open pull request into a conflict, and
buries the next real change in a diff nobody can review. The style is frozen
because unfreezing it is expensive, not because it is right.

The rule that follows from that: **match the file you are editing.** A patch
that fixes one line and reindents the surrounding forty is a patch a reviewer
has to read twice.

#### Indent with tabs

One tab per level. PSR-12 asks for four spaces; core does not use them. Of the
2,870 PHP files under `core/components`, 2,809 indent with tabs and 32 with
spaces.

Newer framework files written from scratch — `Hubzero\Base\ClassLoader`, for
one — use four spaces. Match the file you are editing. Never mix the two in a
single file, and never reindent a file you are otherwise only patching: it
buries the change in a diff nobody can read.

#### Braces on their own line

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

#### A leading underscore is allowed

PSR-12 says an underscore prefix has no meaning. Core uses one on protected and
private members throughout — 887 methods and 718 properties under
`core/components` and `core/libraries`. Do not add the prefix to new code;
do not strip it from existing code either, because the name is part of the
class's contract with its subclasses.

That last part is the reason this one is not simply a tidy-up waiting to
happen. Renaming `_sortNames()` to `sortNames()` in a base class silently
breaks every subclass that overrides it — including subclasses in extensions
that are not in this repository, on hubs you cannot see. PHP raises nothing;
the override just stops being an override and the base implementation runs
instead. The prefix stays.

### Files

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

#### The direct-access gate

A file that is reached by including it — a view template, a plugin entry point,
a helper the router pulls in — declares the gate immediately after the header:

```php
// No direct access
defined('_HZEXEC_') or die;
```

3,389 files do. A file that is only ever reached through the autoloader does
not need it; the autoloader will not run arbitrary code on request.

#### Importing global facades

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

### Lines

The soft limit is 120 characters. There is no hard limit: a longer line is a
warning, never an error, and readability wins over the count. Split a long call
by pulling its arguments into variables first.

No trailing whitespace. One statement per line.

### Strings

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

### Arrays

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

### Classes

Name classes as [PHP Naming Conventions](#php-naming-conventions) describes.
`extends` and `implements` stay on the class line:

```php
class Entries extends SiteController
{
}
```

Declare visibility on every property and method. `var` is not used. Properties
come before methods.

### Functions and methods

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

### Control structures

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

### Documentation blocks

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

### SQL

Keywords uppercase, identifiers lowercase and backquoted. Write the table
prefix as the `#__` placeholder, never a literal `jos_` — see
[Database Schema](#database-schema-conventions).

```php
$this->db->setQuery("SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote($title));
```

Quote every value through `$db->quote()` or bind it. Prefer the query builder
over hand-written SQL in new code; see
[Database](06-database.md).

### Checking your work

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

### When the linter and the house style disagree

phpcs has no committed ruleset here, so there is nothing in the repository
that encodes the house style. `--standard=PSR12` is the closest thing
available and it is wrong about three rules, which is why the exclude list
above exists. When a finding and this page disagree:

1. **The three departures win.** Tabs, braces on their own line, and a
   leading underscore on an existing non-public member are the house style.
   phpcs is not configured to know that. Exclude the sniff, do not change the
   code.
2. **Everything else, phpcs wins.** Trailing whitespace, a missing visibility
   modifier, a space before a comma, `else if` for `elseif` — fix those. They
   are PSR-12 and they are also what this page asks for.
3. **The file you are in wins over both, for anything cosmetic.** A file
   already written with four-space indentation or `[]` arrays stays that way.
   Consistency inside one file beats consistency across the tree, because the
   reader is looking at one file.
4. **When it is still not clear, leave it.** A style question that needs a
   decision is not worth holding a fix for. Say in the pull request that you
   left it and why; a reviewer can settle it in one comment.

Do not add a ruleset to the repository as part of an unrelated change. A
committed `phpcs.xml` would decide the house style for everyone, and that is
its own pull request with its own discussion.

### What the build checks

Three GitHub Actions workflows run on a pull request, each on the paths it
cares about.

| Workflow | Checks |
|---|---|
| [`php-lint.yml`](../../.github/workflows/php-lint.yml) | `php -l` over every `*.php` under `core` and `app` outside `vendor`, then `tools/lint/missing-facade-imports.php`, then `tools/lint/undefined-language-keys.php` against a ceiling of 444 |
| [`tests.yml`](../../.github/workflows/tests.yml) | The PHPUnit suite, for a change under `core` |
| [`pages.yml`](../../.github/workflows/pages.yml) | Builds the documentation, regenerates `docs/reference`, checks every internal link, and fails if the committed `gh-pages/public` is stale |

None of them runs phpcs. A style problem is caught in review, not by the
build. A syntax error, a missing facade import, a language key nothing
defines, and a broken test are caught by the build.

## PHP Naming Conventions

A class name in Hubzero is also a file path. Get the name wrong and the
autoloader does not find the file, so these rules are not decoration — they are
how the code loads.

Two autoloaders are in play:

- **Composer**, configured in [`core/composer.json`](../../core/composer.json),
  handles PSR-4 for exactly two prefixes: `Hubzero\` maps to
  `core/libraries/Hubzero/`, and `Bootstrap\` maps to `core/bootstrap/`.
- **[`Hubzero\Base\ClassLoader`](../../core/libraries/Hubzero/Base/ClassLoader.php)**,
  registered from `core/bootstrap/app.php`, handles everything else:
  `Components\`, `Modules\`, `Plugins\`, `Templates\` and `Migrations\`, plus a
  fallback for the two Composer prefixes.

### The framework: `Hubzero\`

`Hubzero\User\Profile` is `core/libraries/Hubzero/User/Profile.php`. The
namespace maps to the directory one segment at a time, PSR-4 style, and the
file name matches the class name exactly, including case.

Class names contain only letters and digits. An underscore is not a word
separator; it never appears in a framework class name.

Capitalise the first letter of each word, and only the first letter of each
word, even for an acronym. `Hubzero\Utility\Uri`, not `URI`. The library has
`Api`, `Oauth`, `Html`, `Http` and `Htmx` directories; `XCache` is the single
exception in the tree.

Code that is not distributed by Hubzero must never use the `Hubzero\` prefix.

### Components: `Components\`

The first segment after `Components\` is the component name without its
`com_` prefix. The rest of the namespace, plus the class name, is the path
under the component directory.

```
Components\Blog\Site\Controllers\Entries
  -> core/components/com_blog/Site/Controllers/Entries.php
  -> core/components/com_blog/site/controllers/entries.php   (what exists)
```

The loader tries the path as written and then the whole path lowercased. Core
directories and file names are lowercase, so the second attempt is the one that
succeeds. This is why the namespace is `Site\Controllers` while the directory is
`site/controllers`.

Where a namespace has no segment beyond the component name, the class name
supplies the file: `Components\Blog\Blog` resolves to `com_blog/blog.php`.

The loader also tries the directory without the `com_` prefix
(`core/components/blog/…`), and it looks in `PATH_APP` before `PATH_CORE`.
A component present under `app/components` owns that name outright — the core
copy is not consulted for any of its classes.

The segments in use across core:

| Namespace | Holds |
|---|---|
| `Components\[Name]\` | the component's own top-level classes |
| `Components\[Name]\Models\` | models |
| `Components\[Name]\Admin\` | administrator controllers, views and helpers |
| `Components\[Name]\Site\` | site controllers and views |
| `Components\[Name]\Api\` | API controllers |
| `Components\[Name]\Helpers\` | shared helpers |
| `Components\[Name]\Tests\` | unit tests |

#### Entry file

A component's entry file is named for the component without its `com_` prefix
and sits in the client directory: `com_blog/site/blog.php`,
`com_blog/admin/blog.php`. `Hubzero\Component\Loader` builds that path from the
request and includes it. 41 of the 57 core components have one.

The entry file picks a controller from the request and runs it:

<!--include: core/components/com_blog/site/blog.php:8-22-->

A component with no entry file falls through to
`Hubzero\Component\Loader::executeDefault()`, which instantiates
`Components\[Name]\Site\Controllers\[Controller]` directly, defaulting the
controller name to the component name and synthesising an empty controller
extending `Hubzero\Component\DefaultSiteController` when the file is absent.
Either way the class the loader reaches for is the same, so the name has to be
right.

#### Controllers

Name each controller for what it manages, in the plural, and put it under
`Controllers`:

```php
namespace Components\Blog\Site\Controllers;

use Hubzero\Component\SiteController;

class Entries extends SiteController
{
}
```

```
com_blog
   site
      controllers
         entries.php
         media.php
```

Site controllers extend `Hubzero\Component\SiteController`, administrator
controllers `Hubzero\Component\AdminController`, and API controllers
`Hubzero\Component\ApiController`.

#### Models

```php
namespace Components\Blog\Models;

use Hubzero\Database\Relational;

class Entry extends Relational
{
}
```

Models are singular and live in `com_blog/models/entry.php`. `models/` sits
beside `site/` and `admin/`, not inside either, because both clients use it.

#### Views and layouts

A view directory is named for the controller that renders it, and its layout
files sit in a `tmpl/` directory below:

```
com_blog
   site
      views
         entries
            tmpl
               display.php
               entry.php
               edit.php
```

A layout is markup and display logic only: no functions, no classes, so no
class name to get right. The default layout is `display`.

A layout may be accompanied by an XML manifest of the same name, which is what
makes it selectable from a menu item. It is optional and most layouts do not
have one — 285 of the 1,140 layout files in core carry one.

### Plugins: `Plugins\`

A plugin's entry class is **not** namespaced. It is a global class named
`plg[Folder][Element]`, in `core/plugins/[folder]/[element]/[element].php`:

```php
class plgContentFormathtml extends \Hubzero\Plugin\Plugin
{
}
```

330 plugins in core follow this. `Hubzero\Plugin\Loader` reads the plugin's
`folder` and `element` from `#__extensions`, includes
`plugins/[folder]/[element]/[element].php`, and then looks for a class named
`plg` + folder + element. PHP class names are case-insensitive, so
`plgContentFormathtml` matches the lower-case values in the table. Get the name
wrong and the file loads but nothing is instantiated — silently.

The loader will also accept a namespaced entry class named
`Plugins\[Folder]\[Element]`, but no plugin in core uses that form.

Supporting classes that a plugin ships alongside its entry point may be
namespaced, as `Plugins\[Folder]\[Element]\[Name]`, and about seventy files in
core are:

```
Plugins\Content\Formathtml\Macros\Iframe
  -> core/plugins/content/formathtml/Macros/Iframe.php
  -> core/plugins/content/formathtml/macros/iframe.php   (what exists)
```

The folder and element segments are lowercased before the path is built, so
`Plugins\Content\Formathtml\…` reaches `plugins/content/formathtml/`. There is
no `plg_` directory prefix to add.

Plenty of plugin helpers are still global classes with no namespace —
`core/plugins/projects/files/helpers/sync.php` declares a bare `Sync`. Those are
reached by an explicit `require_once`, not by the autoloader. Namespace new
ones.

### Modules: `Modules\`

```
Modules\Login\Helper
  -> core/modules/mod_login/Helper.php
  -> core/modules/mod_login/helper.php   (what exists)
```

The first segment is the module name without `mod_`. As with components, the
loader also tries the directory without the prefix.

On this branch a module is two files. `mod_login.php` is a stub that
`Hubzero\Module\Loader` includes; it requires `helper.php` and calls the class:

<!--include: core/modules/mod_login/mod_login.php:8-12-->

The class name is `Helper` in namespace `Modules\[Name]`. 201 module files
carry a `Modules\` namespace and every module still has its `helper.php`.

### Templates: `Templates\`

```
Templates\Kameleon\Helper
  -> core/templates/tpl_kameleon/Helper.php
  -> core/templates/kameleon/Helper.php    (fallback, and what exists)
```

Core template directories carry no `tpl_` prefix, so the fallback is the path
that resolves. No template in core declares a `Templates\` class; a template's
PHP is `index.php`, `component.php` and `error.php`, included by the document
renderer rather than autoloaded. The prefix is there for templates that want
it.

### Migrations

`ClassLoader` registers a `Migrations\` prefix pointing at `core/migrations`,
but nothing in core uses it: every migration class is global.

A migration class is named `Migration` + a fourteen-digit UTC timestamp + the
extension it changes, in PascalCase with the `com_`/`plg_`/`mod_` prefix folded
in:

```
Migration20260708160000ComEvents
Migration20250207171453PlgAuthfactorsAuthy
Migration20260129000000Core
```

The file is the class name plus `.php`, in `core/migrations/` for a core
migration or in the extension's own `migrations/` directory. It extends
[`Hubzero\Content\Migration\Base`](../../core/libraries/Hubzero/Content/Migration/Base.php).
See [Migrations](06-database.md#migrations).

### Files

Only letters, digits, underscores and hyphens. No spaces. Any file containing
PHP ends in `.php`.

A dot inside a file name breaks the autoloader, which builds the path from the
class name and appends a single `.php`. `grade.book.php` cannot be reached;
`gradebook.php` can.

### Functions and methods

Letters and digits only, `camelCase`, starting lowercase. Be verbose:
`getElementById()` beats `getEl()`.

An accessor for a property is prefixed `get` or `set`. A method implementing a
named pattern says so — `getInstance()` for a singleton.

Controller tasks end in `Task`: a request for `task=entry` calls `entryTask()`.
Plugin event handlers start with `on`: `onContentPrepare()`.

A leading underscore on a protected or private method is the one place an
underscore is allowed. It is legacy — see
[PHP Coding Style](#php-coding-style) —
and new code should not add one. A public method never has one.

Functions in the global scope are permitted but discouraged. The bootstrap
declares a handful (`app()`, `config()`, `with()`); everything else belongs on
a class.

### Variables

Letters and digits, `camelCase`, starting lowercase. As with methods, a leading
underscore on a protected or private property is legacy and tolerated, never on
a public one.

Name for what the variable holds. `$i` and `$n` are fine as loop indices in a
few lines and wrong in a loop long enough that the reader has forgotten them.

```php
namespace Hubzero\Base;

class Example
{
	private $_status = null;

	protected $_fieldName = null;

	protected function _sortNames()
	{
		$someNames = array();
	}
}
```

### Constants

Letters, digits and underscores, all upper case, words separated by
underscores: `EMBED_SUPPRESS_EMBED_EXCEPTION`, not
`EMBED_SUPPRESSEMBEDEXCEPTION`.

Declare a constant as a class member with `const`. `define()` in the global
scope is permitted and strongly discouraged; the ones core does define are
platform-wide (`_HZEXEC_`, `PATH_ROOT`, `PATH_APP`, `PATH_CORE`, `DS`).

### Language keys

Language keys are not PHP identifiers, but they follow the same shape and the
same reasoning: a key nobody defined renders as itself on the page. Prefix the
key with the extension that owns it, upper case, underscore separated:
`COM_BLOG_ENTRY_DELETED`, `PLG_MEMBERS_BLOG_TITLE`. Define it in that
extension's `en-GB` file, not a sibling's.

## CSS Coding Style

Stylesheets live in two places: a template's `css/` directory
(`core/templates/kimera/css/`, `core/templates/kameleon/css/`) and an
extension's own asset directory
(`core/components/com_blog/site/assets/css/blog.css`). The rules below apply to
both, and to the LESS sources the templates compile from.

### Terminology

```css
selector {
	property: value;
}
```

### The file header

A stylesheet opens with the same docblock as a PHP file — `@package`,
`@copyright`, `@license` — in a `/** */` block:

<!--include: core/components/com_blog/site/assets/css/blog.css:1-5-->

### Indentation

One tab per level. 215 of the 252 stylesheets in core indent with tabs, as do
106 of the 135 LESS files.

Rules are indented one level under the comment that introduces their group, so
the comments read as headings down the left margin:

```css
/* Entries listing */
	.blog-entries article {
		position: relative;
	}

	.blog-entries dl.entry-meta {
		margin: 0.5em 0;
		color: #999;
	}
```

### Selectors

A selector sits on one line and ends in the opening brace. The closing brace
goes on its own line.

Where several selectors share a rule, put each on its own line with the comma
immediately after it, no space:

```css
#forum td.posts,
#forum td.topics,
#forum td.replies,
#forum td.pager {
}
```

Leave a blank line between groups of related rules, and comment each group.

### Properties

Each property is on its own line, one level deeper than the selector, with:

- no space before the colon
- one space after the colon
- a semicolon at the end, including on the last property

```css
#forum .description {
	color: #EFEFEF;
	font-size: 0.9em;
	margin: 0.5em;
}
```

Separate multiple values with a space after each comma:

```css
font-family: helvetica, sans-serif;
```

Reach for `!important` only to beat a rule you cannot edit. It appears in core
where a component stylesheet has to override the template.

### LESS

`core/templates/kameleon` and `core/templates/lucent` are written in LESS under
a `less/` directory and compiled into `css/`. The syntax adds three things to
the rules above:

- Variables are `@name`: `font-family: @sansFontFamily;`
- Mixins are called like a rule: `.border-radius(0.25em);`
- Nested blocks and `&` for the parent selector

```less
.input-text,
textarea {
	font-family: @sansFontFamily;
	background-color: #F0F0F0;
	.border-radius(0.25em);

	&:hover {
		border-color: #c9c9c9;
	}

	&:focus {
		background-color: #fff;
		border-color: #777;
	}
}
```

A nested block gets a blank line before it. Keep nesting shallow: every level
is a level of specificity a later rule has to beat.

The compiler is `splitbrain/lesserphp`, wrapped by `core/bin/lessc` for
command-line use and by
[`Hubzero\Document\Assets`](../../core/libraries/Hubzero/Document/Assets.php)
at runtime. The CMS compiles a template's LESS on demand into
`app/cache/site.css`; delete that and `app/cache/site.less.cache` with:

```bash
php core/bin/muse cache:css clear
```

Edit the `.less` source, never the generated `.css` beside it.

### Colours

Hex, and short form where it exists: `#fff`, not `#ffffff`. Core is
inconsistent about case — both `#F0F0F0` and `#c9c9c9` appear — so follow the
file you are in. Use `rgba()` where transparency is wanted, with the flat hex
on the line above as the fallback:

```css
background-color: #F0F0F0;
background-color: rgba(0, 0, 0, 0.039);
```

## Database Schema Conventions

Schema changes reach a hub through a [migration](06-database.md#migrations),
never through a `.sql` file someone runs by hand. This chapter covers the names
a migration should use.

### The table prefix placeholder

Every table name is written with `#__` where the hub's prefix belongs:

```sql
SELECT `id` FROM `#__blog_entries`
```

`Hubzero\Database\Driver::replacePrefix()` rewrites `#__` to the prefix from the
hub's configuration before the statement is sent. The default prefix is `jos_`,
which is why a hardcoded `jos_` appears to work on most hubs and fails on any
hub installed with a different one.

> **Warning:** Never write a literal prefix in a query. It is a live bug class
> in this codebase — four commits on this branch replaced hardcoded prefixes in
> `com_config`, `com_installer`, `plg_groups_forum` and `plg_groups_resources`.
> The only `jos_` strings left in core are in comments and log messages.

`#__` works in raw SQL passed to `$db->setQuery()`, in the query builder's
`from()` and `join()`, and in a `Relational` model's `$table` property.

### Table names

Lowercase, words separated by underscores, prefixed with the extension that
owns the table, and the last word plural:

```
#__blog_entries
#__blog_comments
#__answers_questions
#__answers_responses
#__citations_authors
#__courses_grade_policies
```

The prefix keeps 438 core tables from colliding and makes it obvious which
extension to look in when a query goes wrong. The oldest tables predate the
convention — `#__users`, `#__categories`, `#__assets`, `#__content` — and are
not renamed.

The rule is not only a convention: a `Hubzero\Database\Relational` model that
does not set `$table` builds one from its own name.

```php
$namespace   = (!$this->namespace ? '' : $this->namespace . '_');
$plural      = \Hubzero\Utility\Inflector::pluralize(strtolower($this->getModelName()));
$this->table = $this->table ?: '#__' . $namespace . $plural;
```

`Components\Blog\Models\Entry` with `protected $namespace = 'blog';` therefore
reads `#__blog_entries`. Name the table to match the model and you write no
`$table` property at all.

Where the name is several words, only the last is plural: `application_functions`,
`application_function_roles`. Core is not uniform here; `#__answers_questions`
and `#__cart_carts` pluralise more than the last word. Follow the rule in new
tables and leave the existing names alone.

A table that links two others carries the `_assoc` suffix on the owning
extension's name: `#__citations_assoc`, `#__citations_sponsors_assoc`,
`#__author_assoc`.

### Column names

Lowercase, singular, words separated by underscores: `first_name`,
`order_amount`, `created_by`.

A set of column names recurs across core and carries the same meaning
everywhere. Use them rather than inventing a synonym:

| Column | Meaning |
|---|---|
| `id` | surrogate primary key, `int unsigned AUTO_INCREMENT` |
| `created`, `created_by` | creation timestamp and the user id behind it |
| `modified`, `modified_by` | last change and who made it |
| `state` | publication state; `Relational` defines 0 unpublished, 1 published, 2 deleted, and a model may add its own above those |
| `access` | the viewing level id the row requires |
| `ordering` | manual sort position |
| `params` | the row's own settings, JSON |
| `alias` | the URL-safe form of the title |
| `publish_up`, `publish_down` | the window the row is visible in |
| `checked_out`, `checked_out_time` | edit lock |

A foreign key is the singular of the table it points at plus `_id`: `entry_id`
in `#__blog_comments` points at `#__blog_entries`, `created_by` at `#__users`.

### Indexes

An index is named `idx_` plus the columns it covers:

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_created_by` (`created_by`);
```

For a multi-column index, list the columns in order of cardinality and join
their names:

```sql
ALTER TABLE `#__my_table` ADD INDEX `idx_category_referenceid` (`category`, `reference_id`);
```

A unique index is `uidx_`; a fulltext index is `ftidx_`:

```sql
ALTER TABLE `#__my_table` ADD UNIQUE `uidx_alias` (`alias`);
ALTER TABLE `#__my_table` ADD FULLTEXT `ftidx_content` (`content`);
```

Core holds 1,162 `idx_`, 53 `ftidx_` and 25 `uidx_` index names, so this one is
followed closely.

### A table in a migration

Put the whole definition in one `CREATE TABLE`, guarded by `tableExists()` so
the migration is safe to re-run:

<!--include: core/components/com_blog/migrations/Migration20170901000000ComBlog.php:23-52-->

Give every column an explicit `DEFAULT`. A `NOT NULL` column with no default
makes an insert that omits it fail; `Migration20260129000000Core` exists only to
undo three of those in `#__xprofiles`.

Older migrations write `ENGINE=MyISAM`; new tables should use `ENGINE=InnoDB`
for foreign keys and transactions, unless the table needs a `FULLTEXT` index on
a MySQL old enough not to support one on InnoDB.

The `down()` method reverses what `up()` did. Where reversing would destroy
data, say so in `down()` and do nothing rather than dropping the column.

## Commit Messages

A commit message has a subject line and, for anything but a one-word fix, a
body. The subject says what changed and where. The body says why, and what the
reader would otherwise have to reconstruct from the diff.

### The subject

```
<extension>: <what the change does>
```

The prefix names the extension the change belongs to, written the way the
codebase writes it: `com_members`, `plg_editors_ckeditor5`, `mod_login`. Where a
change is not in one extension, use the subsystem: `Database`, `Console`,
`Http`, `Filesystem`, `Component`, `Plugin`, `Plugins`, `Documentation`. A
change spanning two extensions names both, comma separated.

Then a sentence. Capitalised, no full stop, describing what the commit does
rather than what was wrong.

```
com_cart: Add the Items Ordered heading string
com_wiki: Label the page state field with its real states
plg_groups_forum: Use the table prefix placeholder
Database: Read a column default from the variable that holds it
com_content, com_categories: Return early when no items are selected
```

Keep the subject under about 72 characters. The last two hundred commits on
this branch have a median subject of 57 characters and a longest of 89; the
50-character target older documentation gave is not what the history does, and
squeezing a sentence into 50 costs more clarity than it buys.

Do not use `[feat]`, `[fix]`, `[refactor]`, `[style]`, `[docs]` or `[test]`
tags. The repository carries 1,559 commits with them, all older; none of the
last three hundred. The extension prefix replaced them and says more.

`[PR #1234]` prefixes appear on merges made through the GitHub interface and
are added by the merge, not typed by hand.

### The body

Separate it from the subject with a blank line and wrap it at 72 characters.

Say why the change is needed before saying what it does. A reader six months
from now has the diff already; what they do not have is the reason, the
symptom, and what you ruled out.

```
com_projects: Fix the FERPA description key

The component manifest reads COM_PROJECTS_CONFIG_FERPALINK_DESC while
the language file defined COM_PROJECTS_CONFIG_FERPAALINK_DESC, so the
options screen rendered the raw key and the defined string was dead.
```

Three things worth writing down:

- **The symptom.** What a user or administrator saw. "The heading rendered as
  the raw key", not "fixed a string".
- **The mechanism.** Why the code did that. Name the file, the condition, the
  key.
- **What you did not change.** If a fix is deliberately narrow, or a related
  fault is left for a separate decision, say so. It stops the next person
  re-investigating.

A commit that only reformats code, or only renames things, gets its own commit
and says so. Never mix a behaviour change with a cleanup pass; the reviewer
cannot see the one for the other.

### References

If a change fixes a reported issue or follows from an outside discussion, name
it in the body:

```
Fixes: https://help.hubzero.org/support/ticket/12345
Refs: https://github.com/hubzero/hubzero-cms/pull/1923
```

Neither trailer appears in the last three hundred commits, so it is a
convention available to you rather than one in daily use. GitHub's own
`Fixes #123` closes an issue in this repository when the commit lands.

### A commit template

`git` will pre-fill the editor from a template. Point `commit.template` at one
in `~/.gitconfig`:

```ini
[commit]
	template = ~/.gitmessage
```

Then write `~/.gitmessage`:

```
#--------------------------------72----------------------------------|
# <extension>: <what the change does>
#
# Why the change is needed, what the symptom was, and what it does not
# cover. Wrap at 72.
#
# Fixes:
# Refs:
```

### Before you commit

Run the linters over what you changed. See
[PHP Coding Style](#php-coding-style) for the commands.
If the change touches `docs/`, run `sh tools/docs/rebuild.sh` and commit the
rebuilt `gh-pages/public/` with it; the Pages workflow fails on a stale copy.
