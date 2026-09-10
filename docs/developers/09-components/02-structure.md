<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/structure
-->
# Structure

A component is a directory named `com_{componentname}`, split first by client
and then by role. This page shows the layout of a real one, explains how class
names map onto it, and follows a request from the URL to the controller.

Almost nothing here is configurable, and that is the point: put a file in the
place the convention names and it is found, with no registration anywhere. Put
it a directory to one side and nothing reports it — the class simply does not
exist, and the first line that mentions it is a fatal.

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

`com_bookings` is the same shape with two clients and no API:

```
app/components/com_bookings/
    bookings.xml
    composer.json
    config/config.xml  config/access.xml
    migrations/Migration20260901000000ComBookings.php ...
    models/
        instrument.php              Components\Bookings\Models\Instrument
        reservation.php             Components\Bookings\Models\Reservation
    site/
        bookings.php  router.php
        controllers/instruments.php  controllers/reservations.php
        views/instruments/tmpl/display.php  view.php  book.php
        assets/css/bookings.css
        language/en-GB/en-GB.com_bookings.ini
    admin/
        bookings.php
        controllers/instruments.php  controllers/reservations.php
        views/instruments/tmpl/display.php  edit.php
        language/en-GB/
            en-GB.com_bookings.ini
            en-GB.com_bookings.sys.ini
```

## Namespaces and the class loader

Every component class lives under the `Components` namespace, and the segment
after it is the studly-cased component name without the `com_` prefix:

```
Components\Bookings\Site\Controllers\Instruments
Components\Bookings\Models\Reservation
Components\Kb\Admin\Helpers\Permissions
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
form, and so should yours; the mixed-case path is a fallback, not a choice
worth making.

> **Note:** Whichever root holds the extension owns it. If
> `app/components/com_kb/` exists, no class is ever loaded from
> `core/components/com_kb/` — not even one the app copy does not define. This
> is why a partial copy of a core component is a broken copy: the loader finds
> the app directory, stops looking, and every class you did not copy is
> missing.

Because classes autoload, `require_once` on a component class is redundant.
Several shipped components still do it — `com_kb`'s entry points among them —
which is harmless but is inherited practice, not the current one. Leave it out
of new code.

### The name is load-bearing

`SiteController`'s constructor derives the component from the **class
namespace**, not from the directory or the request: it explodes the class name
and takes the second segment. `Components\Bookings\Site\Controllers\Instruments`
gives `$this->_name = 'bookings'` and `$this->_option = 'com_bookings'`.

Get the namespace segment and the directory out of step — a class declared
`namespace Components\Booking\...` inside `com_bookings/` — and nothing throws.
The controller quietly reports itself as `com_booking`: it loads that
component's parameters (empty), looks for `com_booking`'s language file
(missing, so every string renders as its own key), and `Route::url()` builds
URLs into a component that is not there.

## The entry point

The URL carries the component in `option`:

```
index.php?option=com_bookings&task=view&instrument=confocal
```

With search engine friendly URLs on, the first path segment names the
component instead, and the rest is handed to the component's router:

```
/bookings/confocal
```

Either way the application ends at `Component::render('com_bookings')` in
[`Hubzero\Component\Loader`](../../../core/libraries/Hubzero/Component/Loader.php).
That method defines `PATH_COMPONENT` (the client directory,
`com_bookings/site` or `com_bookings/admin`), plus `PATH_COMPONENT_SITE` and
`PATH_COMPONENT_ADMINISTRATOR`, loads the component's language files, and then
looks for something to run, in this order:

1. a `Components\{Name}\{Client}\Bootstrap` class, whose `start()` method is
   called;
2. `PATH_COMPONENT/{componentname}.php`, which is simply `require`d;
3. a directory `PATH_COMPONENT/assets/react/{componentname}`, run as a React
   application;
4. failing all of those, a `Hubzero\Component\DefaultSiteController` is
   synthesised for the requested controller.

Every component in the tree today uses the second form, and it is the one to
write. Here is the whole of `com_kb`'s site entry point:

<!--include: core/components/com_kb/site/kb.php:8-15-->

That is the minimum: a namespace, a controller, `execute()`. (The two
`require_once` lines above it in the file are the inherited habit noted above;
the class loader covers both.) The administrator entry point does more,
because the administrator needs an authorisation gate and a sub-menu:

<!--include: core/components/com_kb/admin/kb.php:8-41-->

Three things are worth copying from it. The `core.manage` check is what keeps
non-managers out of the whole client — no controller sees the request without
it, and leaving it out means every logged-in user can reach the administrator
screens by typing the URL. `Submenu::addEntry()` builds the row of links under
the toolbar. And the controller name comes from the request, with a fallback,
so `index.php?option=com_bookings&controller=reservations` reaches
`Components\Bookings\Admin\Controllers\Reservations`.

> **Note:** Entry point files are namespaced, so the facades they call must be
> imported or fully qualified. `com_kb`'s administrator entry point writes
> `\User::authorise()` and `\Route::url()` with a leading backslash for
> exactly this reason; see
> [Facades](../03-foundation/06-facades.md#importing-a-facade). The file parses
> either way — the fatal arrives only when the line runs.

Do not close the PHP tag in a file that contains only PHP. A stray newline
after `?>` becomes output, and output before the document is assembled breaks
redirects and headers.

## What the directory alone gets you

Placing the directory on disk is enough to make a component *run*. When
`Component::load()` finds no row in `#__extensions` it builds a default record
with `enabled` set to 1, so the component executes.

What a missing row costs you is everything stored against it: the component
does not appear in the administrator's list of components,
`Component::params()` returns an empty registry, and there is no asset row for
its permissions. A [migration](01-migrations.md) creates that row, and that is
the step to do before anything else.
