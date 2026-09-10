<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/components/packaging
-->
# Packaging

There is no build step and no package format. A distributable component is
the directory exactly as it appears once installed, plus two descriptive files
at its root.

```
com_bookings/
    composer.json
    bookings.xml
    config/
        config.xml
        access.xml
    migrations/
        Migration20260901000000ComBookings.php
    models/
        instrument.php
        reservation.php
    site/
        bookings.php
        router.php
        controllers/instruments.php
        views/instruments/tmpl/display.php
        assets/css/bookings.css
        language/en-GB/en-GB.com_bookings.ini
    admin/
        bookings.php
        controllers/instruments.php
        views/instruments/tmpl/display.php
        views/instruments/tmpl/edit.php
        language/en-GB/en-GB.com_bookings.ini
        language/en-GB/en-GB.com_bookings.sys.ini
```

Copy that directory into `app/components/` and you have something installed.

> **Important:** There is no package installer to hand it to. The
> **Install**, **Update**, **Discover** and **Database** screens were removed
> from this release, so a `.zip` is a way to move the directory between
> machines and nothing more — nothing on the hub will unpack it or read its
> manifest. The two routes that work are the Extension Manager's **Custom
> Extensions** tab, which clones a git repository into `app/`, and copying
> the directory by hand. Both are in
> [Deploying extensions](../07-extensions/04-deployext.md).

The practical consequence: **ship the component as a git repository**, laid
out so that the repository root *is* the component directory. That is what
the supported route consumes, and it is how a hub gets updates afterwards.

## `composer.json`

The package definition names the component and declares its type:

<!--include: core/components/com_kb/composer.json-->

`"type": "hubzero-component"` distinguishes it from a library package. The
`require` block is where a component states what it needs — a PHP version, or
a third-party library it will `use`.

> **Note:** The platform does not ship a Composer installer plugin for
> `hubzero-component`, so `composer require` will not place a component in
> `app/components/` on its own. The file documents the package; the directory
> is deployed by the Extension Manager's git flow, by hand, or by whatever the
> hub uses to deploy code.

Most shipped `composer.json` files still declare `"php": ">=5.4"` or similar.
Those constraints are stale and nothing enforces them. Hubzero 2.4 needs PHP
8.2; write that.

## The manifest

`{componentname}.xml` carries the metadata the administrator displays: name,
author, copyright, licence, description, and version. Its root element is
`<extension type="component">`.

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="component" version="1.5">
    <name>COM_BOOKINGS</name>
    <author>Your Organisation</author>
    <authorUrl>example.org</authorUrl>
    <authorEmail>support@example.org</authorEmail>
    <version>1.0.0</version>
    <copyright>Copyright (c) 2026 Your Organisation</copyright>
    <license>http://opensource.org/licenses/MIT MIT</license>
    <description>COM_BOOKINGS_XML_DESCRIPTION</description>
    <administration>
        <menu>COM_BOOKINGS</menu>
    </administration>
</extension>
```

`<name>` and `<description>` may be language keys, resolved from the
administrator `.sys.ini` file. Write keys unless the component is for one hub
in one language.

The manifest is metadata. It installs nothing, creates nothing and is read by
nothing at request time; a component with no manifest at all still runs. It
exists so the administrator's lists have something to show.

Older manifests, `com_kb`'s included, also carry `<files>`, `<languages>`,
`<install><sql>`, and `<params>` blocks. They are inherited from when a
package installer read them. None of them do anything now, and most are stale
— `com_kb`'s `<files>` list names files that were deleted years ago. Do not
read an existing `<files>` block as a description of the component, and do not
copy one into a new component:

| Old block | What to use instead |
|---|---|
| `<files>` | nothing; the directory is the manifest of itself |
| `<install><sql>` | a [migration](01-migrations.md) |
| `<params>` | [`config/config.xml`](10-configuration.md) |
| `<languages>` | nothing; language files are found by path |

> **Note:** `com_installer` looks for a component's manifest at
> `components/{element}/{element}.xml`, which for `com_kb` is
> `com_kb/com_kb.xml` — not the `kb.xml` that is actually there. Every
> component in the tree is named `{name}.xml`, so the manifest cache is never
> refreshed from the file, and the Extensions manager shows whatever was
> recorded when the row was created. Naming a new component's manifest
> `com_bookings.xml` makes the refresh work; naming it `bookings.xml` matches
> the rest of the tree and does not. Neither choice affects anything else, so
> pick the one that matters to you and know which you picked.

## What actually installs it

Nothing in the package creates database tables or the `#__extensions` row.
That is the job of the component's migrations, run after the files are in
place:

```bash
php core/bin/muse migration -f -e=com_bookings
```

Ship the migrations with the component and installation is one command that
works the same on every hub, is idempotent, and can be rolled back. Leave them
out and the component is invisible to the administrator on every hub it
reaches. See [Migrations](01-migrations.md).

## Where it goes

`app/components/com_bookings/`. Never `core/components/` — that directory is
the platform's, and an upgrade will overwrite it. The class loader and
`Component::path()` both check `app` before `core`, so a component in `app`
takes precedence over a core component of the same name, and everything a hub
adds survives an upgrade untouched.

The full deployment procedure, including the by-hand route and what to do when
the Extension manager cannot write to the filesystem, is in
[Deploying extensions](../07-extensions/04-deployext.md).
