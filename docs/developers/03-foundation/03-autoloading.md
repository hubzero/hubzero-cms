<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
screenshots: none
-->
# Autoloading

How a class name becomes a file on disk. You never `require` a class in an
extension: you write `new Instrument` and something has to find
`components/com_booking/models/instrument.php`. This page says what that
something is, in what order, and what to check when it does not find your
file — which is the reason most people read it.

The failure it explains looks like this, and it is always the same shape:

```text
PHP Fatal error:  Uncaught Error: Class "Components\Booking\Models\Instrument"
not found in /var/www/example/core/components/com_booking/site/controllers/instruments.php:41
```

The class name in the message is the *only* input the loader had. Everything
below is what it did with it.

## Three loaders, in order

They are registered in this order and PHP tries them in this order.

| Order | Loader | Covers |
|---|---|---|
| 1 | Composer's PSR-4 map | `Hubzero\` and `Bootstrap\` only |
| 2 | [`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php) | `Components\`, `Modules\`, `Plugins\`, `Templates\`, `Migrations\`, and a fallback for the first two |
| 3 | [`Hubzero\Facades\Facade::loadAliases`](../../../core/libraries/Hubzero/Facades/Facade.php) | The root-namespace facade aliases. See [Facades](06-facades.md) |

Composer's map is two entries and no more:

<!--include: core/composer.json:95-101-->

That is worth reading twice. **Composer does not know about your component.**
`composer dump-autoload` will never help a component, plugin, module or
template class load, and adding your extension to `core/composer.json` is
not how it is done. Extension classes are the class loader's job.

The class loader is registered in
[`core/bootstrap/app.php`](../../../core/bootstrap/app.php), before anything
else runs:

<!--include: core/bootstrap/app.php:61-62-->

The two directories are the search path, and `PATH_APP` is first, which is
what makes a hub's own copy of an extension win over the shipped one.

## The mapping

`ClassLoader::register()` declares the prefixes:

<!--include: core/libraries/Hubzero/Base/ClassLoader.php:72-82-->

Each one turns the rest of the class name into a path a different way.

| Class | Directory tried | Then |
|---|---|---|
| `Components\Booking\Models\Instrument` | `components/com_booking/` | `components/booking/` |
| `Modules\Booking\Booking` | `modules/mod_booking/` | `modules/booking/` |
| `Plugins\Content\Booking\Helper` | `plugins/content/booking/` | — |
| `Templates\Kimera\Helper` | `templates/tpl_kimera/` | `templates/kimera/` |
| `Migrations\Global\Core\Migration…` | `migrations/` | — |
| `Hubzero\Database\Relational` | `libraries/Hubzero/` | — |

The first segment after the prefix is the extension name, lowercased, and it
never keeps its capitals: `Components\Booking` is `com_booking`, not
`com_Booking`. Everything after it is the path inside the extension, joined
with `/` and given a `.php` suffix. A class with nothing after the extension
name — `Components\Booking\Booking` — looks for `com_booking/Booking.php`.

## Two filename variants, and the trap between them

For each candidate path the loader tries exactly two spellings: the path as
the namespace spells it, and the same path **lowercased in full**.

<!--include: core/libraries/Hubzero/Base/ClassLoader.php:202-207-->

So `Components\Booking\Models\Instrument` is looked for at:

```text
components/com_booking/Models/Instrument.php
components/com_booking/models/instrument.php
```

and nowhere else. There is no third try.

> **Warning:** A capitalised filename in a lowercase directory does not
> autoload. `models/Instrument.php` matches neither variant: the first wants
> `Models/`, the second wants `instrument.php`. On Linux the filesystem is
> case-sensitive and the class is simply not found.

That is not hypothetical. `Components\Storefront\Models\Warehouse` lives in
`core/components/com_storefront/models/Warehouse.php` — lowercase directory,
capitalised file — and cannot be autoloaded, which is why
`core/components/com_storefront/site/storefront.php` opens by requiring the
file by hand:

<!--include: core/components/com_storefront/site/storefront.php:13-14-->

`com_cart` and `com_saml` carry model files in the same shape. Treat those
`require_once` lines as a symptom, not a pattern: pick one spelling for the
whole extension — all-lowercase directories and filenames is the common one
in this tree — and the loader finds everything without help.

## Whichever tree holds the extension owns it

For extension classes the loader first decides *which directory owns the
extension*, then searches only there:

<!--include: core/libraries/Hubzero/Base/ClassLoader.php:229-245-->

`app/` is checked first, so if `app/components/com_booking/` exists, the core
copy is never consulted again — for any class in that namespace.

> **Warning:** An override is whole-extension. Copy one file into
> `app/components/com_booking/models/instrument.php` and every *other*
> `Components\Booking\*` class stops loading, because the app directory now
> owns the namespace and the rest of the files are still in `core/`. The
> symptom is a class-not-found for a class you never touched. Copy the whole
> extension or none of it; to change one view, use a
> [template override](../11-templates/09-overrides.md) instead.

Framework classes are the exception. `Hubzero\` and `Bootstrap\` are searched
across both directories, one file at a time, so an app-level copy of a single
framework file does not hide the rest of the library.

## The per-kind details that catch people

**Modules collapse to one word.** The module directory is `mod_` plus the
namespace segment lowercased, with nothing inserted. `Modules\ArticlesArchive`
is looked for in `modules/mod_articlesarchive`, and the directory in the tree
is `mod_articles_archive`. Sixteen shipped module directories carry an
underscore in the name and none of them can be reached that way. It costs
nothing today, because a module is rendered by including `mod_{name}.php`
rather than by autoloading a class — see
[Extensions](05-extensions.md#modules) — but a class you add to such a module
will not load by name.

**Plugins have one path, not two.** `Plugins\Content\Booking\Helper` is
`plugins/content/booking/Helper.php` (or the all-lowercase spelling). There
is no `plg_` prefix and no second directory to fall back on. The group
directory name and the plugin directory name both come straight from the
class name, lowercased.

**Templates try `tpl_` first.** Every template in the tree is a bare name —
`core/templates/kimera` — so it is always the second variant that matches.

## The legacy underscore fallback

If no prefix matches, the loader falls back to treating the whole class name
as a path, converting **both** backslashes and underscores to separators:

<!--include: core/libraries/Hubzero/Base/ClassLoader.php:290-297-->

That is why the pre-2.0 underscored names still resolve where the files still
exist: `Hubzero_User_Profile` becomes `Hubzero/User/Profile.php`. It is a
compatibility path, not a naming scheme. New classes are namespaced. See the
[Upgrade guide](../01-getting-started/04-upgrade.md).

## Diagnosing a class that will not load

Work the list in order. It is short because the loader is short.

1. **Read the class name in the error, not the one you meant to write.** A
   missing `use` statement makes PHP look for the name inside the current
   namespace, and that name is what you will see in the message. If the name
   has your component's namespace glued to the front of something that should
   be global — `Components\Booking\Site\Controllers\Route` — the problem is an
   import, not the loader. See [Facades](06-facades.md).
2. **Check the extension directory name.** `Components\Booking` needs
   `com_booking`. A directory called `com_bookings` or `com_Booking` is a
   different extension as far as the loader is concerned.
3. **Check the two spellings.** Directory and filename must be *both*
   as-namespaced or *both* lowercase. This is the most common cause.
4. **Check for an `app/` copy.** `ls app/components/com_booking` — if that
   directory exists, `core/` is out of the picture entirely.
5. **Check the namespace in the file itself** matches the directory it is in.
   The loader derives the path from the class name; nothing verifies that the
   file it loads declares the class it was asked for. A file with the wrong
   `namespace` line loads silently and the class is still missing.

`Hubzero\Base\ClassLoader::getDirectories()` and `getPrefixes()` return the
search path and the prefix map if you want to print them from a debug
statement.

> **Note:** Nothing caches these lookups. Every unresolved class name costs
> a handful of `file_exists()` calls against both trees. It is not a
> bottleneck, but it is the reason a missing facade import is more expensive
> than it looks: each call site does the whole sweep before the alias
> autoloader answers.
