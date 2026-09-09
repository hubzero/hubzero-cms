<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/structure
-->
# Structure

A component is a directory named `com_{componentname}`, split first by client
and then by role. This page shows the layout of a real one, explains how class
names map onto it, and follows a request from the URL to the controller.

## The layout

Everything below is from `core/components/com_kb`, with a few files elided:

```
core/components/com_kb/
    kb.xml                          manifest
    composer.json                   package definition
    config/
        config.xml                  the Options form
        access.xml                  the permission actions
    migrations/
        Migration20170831000000ComKb.php
        Migration20170901000000ComKb.php
        Migration20190221000000ComKb.php
    models/
        archive.php                 Components\Kb\Models\Archive
        article.php                 Components\Kb\Models\Article
        category.php                comment.php  vote.php  tags.php
        forms/
            article.xml  category.xml
    site/
        kb.php                      entry point
        router.php                  Components\Kb\Site\Router
        controllers/
            articles.php            Components\Kb\Site\Controllers\Articles
        views/
            articles/tmpl/
                display.php  display.xml
                category.php  category.xml
                article.php   article.xml
                _list.php  _comment.php  _vote.php
        assets/
            css/kb.css
            js/kb.js
        language/en-GB/
            en-GB.com_kb.ini
    admin/
        kb.php                      entry point
        controllers/
            articles.php            Components\Kb\Admin\Controllers\Articles
        helpers/
            html.php                Components\Kb\Admin\Helpers\Html
            permissions.php         Components\Kb\Admin\Helpers\Permissions
        views/
            articles/tmpl/
                display.php  edit.php
        assets/js/kb.js
        help/en-GB/*.phtml          administrator help screens
        language/en-GB/
            en-GB.com_kb.ini
            en-GB.com_kb.sys.ini
            en-GB.com_kb.menu.ini
    api/
        router.php                  Components\Kb\Api\Router
        controllers/
            entriesv1_0.php         Components\Kb\Api\Controllers\Entriesv1_0
```

`config/`, `migrations/`, and `models/` sit above the client directories
because all three clients use them. Everything else belongs to exactly one
client. A component may also carry a `helpers/` directory at the top level for
helpers shared between clients, and a `tests/` directory.

## Namespaces and the class loader

Every component class lives under the `Components` namespace, and the segment
after it is the studly-cased component name without the `com_` prefix:

```
Components\Kb\Site\Controllers\Articles
Components\Kb\Admin\Helpers\Permissions
Components\Kb\Models\Article
```

[`Hubzero\Base\ClassLoader`](../../../core/libraries/Hubzero/Base/ClassLoader.php),
registered in `core/bootstrap/app.php`, resolves those names. It drops the
`Components\` prefix, lowercases the component name and prefixes it with
`com_`, and joins the rest with directory separators:

```
Components\Kb\Models\Article  ->  components/com_kb/Models/Article.php
                              ->  components/com_kb/models/article.php
```

Both the studly-cased and the wholly lowercased path are tried, against
`PATH_APP` first and then `PATH_CORE`. This is the one place the naming
convention bends: **files and directories may be lowercase even when the class
name is not.** Practically every component in the tree uses the lowercase
form.

> **Note:** Whichever root holds the extension owns it. If
> `app/components/com_kb/` exists, no class is ever loaded from
> `core/components/com_kb/` — not even one the app copy does not define.

Because classes autoload, `require_once` on a component class is redundant.
Several shipped components still do it — `com_kb`'s entry points among them —
which is harmless but no longer necessary.

## The entry point

The URL carries the component in `option`:

```
index.php?option=com_kb&task=category&categoryAlias=printing
```

With search engine friendly URLs on, the first path segment names the
component instead, and the rest is handed to the component's router:

```
/kb/printing
```

Either way the application ends at `Component::render('com_kb')` in
[`Hubzero\Component\Loader`](../../../core/libraries/Hubzero/Component/Loader.php).
That method defines `PATH_COMPONENT` (the client directory,
`com_kb/site` or `com_kb/admin`), plus `PATH_COMPONENT_SITE` and
`PATH_COMPONENT_ADMINISTRATOR`, loads the component's language files, and then
looks for something to run, in this order:

1. a `Components\{Name}\{Client}\Bootstrap` class, whose `start()` method is
   called;
2. `PATH_COMPONENT/{componentname}.php`, which is simply `require`d;
3. a directory `PATH_COMPONENT/assets/react/{componentname}`, run as a React
   application;
4. failing all of those, a `Hubzero\Component\DefaultSiteController` is
   synthesised for the requested controller.

Every component in the tree today uses the second form. Here is the whole of
`com_kb`'s site entry point:

<!--include: core/components/com_kb/site/kb.php:8-15-->

That is the minimum: a namespace, a controller, `execute()`. The
administrator entry point does more, because the administrator needs an
authorisation gate and a sub-menu:

<!--include: core/components/com_kb/admin/kb.php:8-41-->

Three things are worth copying from it. The `core.manage` check is what keeps
non-managers out of the whole client — no controller sees the request without
it. `Submenu::addEntry()` builds the row of links under the toolbar. And the
controller name comes from the request, with a fallback, so
`index.php?option=com_kb&controller=articles` reaches
`Components\Kb\Admin\Controllers\Articles`.

> **Note:** Entry point files are namespaced, so the facades they call must be
> imported or fully qualified. `com_kb`'s administrator entry point writes
> `\User::authorise()` and `\Route::url()` with a leading backslash for
> exactly this reason; see
> [Facades](../03-foundation/04-facades.md#importing-a-facade).

Do not close the PHP tag in a file that contains only PHP. A stray newline
after `?>` becomes output, and output before the document is assembled breaks
redirects and headers.

## Installation

Placing the directory on disk is enough to make a component run. When
`Component::load()` finds no row in `#__extensions` it builds a default record
with `enabled` set to 1, so the component executes.

What a missing row costs you is everything stored against it: the component
does not appear in the administrator's list of components, `Component::params()`
returns an empty registry, and there is no asset row for its permissions. A
[migration](migrations.md) creates that row.
