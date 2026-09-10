<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/components/packaging
-->
# Packaging

There is no build step. A distributable component is the directory exactly as
it appears once installed, plus two descriptive files at its root.

```
com_example/
    composer.json
    example.xml
    config/
        config.xml
        access.xml
    migrations/
        Migration20260901000000ComExample.php
    models/
        thing.php
    site/
        example.php
        router.php
        controllers/things.php
        views/things/tmpl/display.php
        assets/css/example.css
        language/en-GB/en-GB.com_example.ini
    admin/
        example.php
        controllers/things.php
        views/things/tmpl/display.php
        views/things/tmpl/edit.php
        language/en-GB/en-GB.com_example.ini
        language/en-GB/en-GB.com_example.sys.ini
```

Zip that directory and you have something installable. Unzip it into
`app/components/` and you have something installed. The
[Extension manager](../../managers/10-extensions/04-extension-manager.md) does
the same thing with a click.

## `composer.json`

The package definition names the component and declares its type:

<!--include: core/components/com_kb/composer.json-->

`"type": "hubzero-component"` distinguishes it from a library package. The
`require` block is where a component states what it needs — a PHP version, or
a third-party library it will `use`.

> **Note:** The platform does not ship a Composer installer plugin for
> `hubzero-component`, so `composer require` will not place a component in
> `app/components/` on its own. The file documents the package; the directory
> is deployed by the Extension manager, by hand, or by whatever the hub uses
> to deploy code.

## The manifest

`{componentname}.xml` carries the metadata the administrator displays: name,
author, copyright, licence, description, and version. Its root element is
`<extension type="component">`.

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="component" version="1.5">
    <name>Example</name>
    <author>Your Organisation</author>
    <authorUrl>example.org</authorUrl>
    <authorEmail>support@example.org</authorEmail>
    <version>1.0.0</version>
    <copyright>Copyright (c) 2026 Your Organisation</copyright>
    <license>http://opensource.org/licenses/MIT MIT</license>
    <description>Manage examples</description>
    <administration>
        <menu>Examples</menu>
    </administration>
</extension>
```

Older manifests, `com_kb`'s included, also carry `<files>`, `<languages>`,
`<install><sql>`, and `<params>` blocks. None of them do anything now, and
most are stale — `com_kb`'s `<files>` list names files that were deleted years
ago. Do not copy them into a new component:

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
> `com_example.xml` makes the refresh work.

## What actually installs it

Nothing in the package creates database tables or the `#__extensions` row.
That is the job of the component's migrations, run after the files are in
place:

```bash
php core/bin/muse migration -f -e=com_example
```

Ship the migrations with the component and installation is one command that
works the same on every hub, is idempotent, and can be rolled back. See
[Migrations](01-migrations.md).

## Where it goes

`app/components/com_example/`. Never `core/components/` — that directory is
the platform's, and an upgrade will overwrite it. The class loader and
`Component::path()` both check `app` before `core`, so a component in `app`
takes precedence over a core component of the same name, and everything a hub
adds survives an upgrade untouched.

The full deployment procedure, including the by-hand route and what to do when
the Extension manager cannot write to the filesystem, is in
[Deploying extensions](../07-extensions/04-deployext.md).
