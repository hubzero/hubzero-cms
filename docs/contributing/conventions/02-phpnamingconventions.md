<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/conventions/phpnamingconventions
-->
# PHP Naming Conventions

A class name in Hubzero is also a file path. Get the name wrong and the
autoloader does not find the file, so these rules are not decoration — they are
how the code loads.

Two autoloaders are in play:

- **Composer**, configured in [`core/composer.json`](../../../core/composer.json),
  handles PSR-4 for exactly two prefixes: `Hubzero\` maps to
  `core/libraries/Hubzero/`, and `Bootstrap\` maps to `core/bootstrap/`.
- **[`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php)**,
  registered from `core/bootstrap/app.php`, handles everything else:
  `Components\`, `Modules\`, `Plugins\`, `Templates\` and `Migrations\`, plus a
  fallback for the two Composer prefixes.

## The framework: `Hubzero\`

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

## Components: `Components\`

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

### Entry file

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

### Controllers

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

### Models

```php
namespace Components\Blog\Models;

use Hubzero\Database\Relational;

class Entry extends Relational
{
}
```

Models are singular and live in `com_blog/models/entry.php`. `models/` sits
beside `site/` and `admin/`, not inside either, because both clients use it.

### Views and layouts

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

## Plugins: `Plugins\`

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

## Modules: `Modules\`

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

## Templates: `Templates\`

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

## Migrations

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
[`Hubzero\Content\Migration\Base`](../../../core/libraries/Hubzero/Content/Migration/Base.php).
See [Migrations](../../developers/06-database.md#migrations).

## Files

Only letters, digits, underscores and hyphens. No spaces. Any file containing
PHP ends in `.php`.

A dot inside a file name breaks the autoloader, which builds the path from the
class name and appends a single `.php`. `grade.book.php` cannot be reached;
`gradebook.php` can.

## Functions and methods

Letters and digits only, `camelCase`, starting lowercase. Be verbose:
`getElementById()` beats `getEl()`.

An accessor for a property is prefixed `get` or `set`. A method implementing a
named pattern says so — `getInstance()` for a singleton.

Controller tasks end in `Task`: a request for `task=entry` calls `entryTask()`.
Plugin event handlers start with `on`: `onContentPrepare()`.

A leading underscore on a protected or private method is the one place an
underscore is allowed. It is legacy — see
[PHP Coding Style](01-phpcodingstyles.md#a-leading-underscore-is-allowed) —
and new code should not add one. A public method never has one.

Functions in the global scope are permitted but discouraged. The bootstrap
declares a handful (`app()`, `config()`, `with()`); everything else belongs on
a class.

## Variables

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

## Constants

Letters, digits and underscores, all upper case, words separated by
underscores: `EMBED_SUPPRESS_EMBED_EXCEPTION`, not
`EMBED_SUPPRESSEMBEDEXCEPTION`.

Declare a constant as a class member with `const`. `define()` in the global
scope is permitted and strongly discouraged; the ones core does define are
platform-wide (`_HZEXEC_`, `PATH_ROOT`, `PATH_APP`, `PATH_CORE`, `DS`).

## Language keys

Language keys are not PHP identifiers, but they follow the same shape and the
same reasoning: a key nobody defined renders as itself on the page. Prefix the
key with the extension that owns it, upper case, underscore separated:
`COM_BLOG_ENTRY_DELETED`, `PLG_MEMBERS_BLOG_TITLE`. Define it in that
extension's `en-GB` file, not a sibling's.
