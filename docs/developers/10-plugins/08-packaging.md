<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/packaging
-->
# Packaging

A plugin ships as its own directory, laid out exactly as it will sit on disk.
Two manifests describe it: `composer.json` for the package manager, and
`{name}.xml` for the CMS, which reads the display name and the parameter form
from it.

## Package layout

```
plg_system_example/
    assets/css/example.css
    language/en-GB/en-GB.plg_system_example.ini
    language/en-GB/en-GB.plg_system_example.sys.ini
    migrations/Migration20260101000000PlgSystemExample.php
    views/display/tmpl/default.php
    composer.json
    example.php
    example.xml
    index.html
```

The package directory is conventionally named `plg_{group}_{name}`, but what
lands on disk is `plugins/{group}/{name}` — the group is not a directory
inside the package, it is where the package is installed to.

## The Composer manifest

```json
{
	"name": "myorg/plg_system_example",
	"description": "Example system plugin for the Hubzero CMS",
	"type": "hubzero-plugin",
	"keywords": ["hubzero"],
	"homepage": "https://example.org",
	"license": "MIT",
	"require": {
		"php": "^5.4"
	},
	"extra": {
		"install-directory": "/plugins/system/example/"
	}
}
```

The `type` must be `hubzero-plugin`; the recognised types are
`hubzero-component`, `hubzero-module`, `hubzero-plugin`, and
`hubzero-template`.

> **Important:** `extra.install-directory` is required for a plugin and only
> for a plugin. Every other extension type can be placed from its package name
> alone, but a plugin's group cannot be inferred from `plg_system_example` —
> the group and the element are both in there, with nothing to say where one
> ends. `plg_members_blog` in `core/plugins` declares
> `"/plugins/members/blog/"`.

A plugin that extends a component should require it, so the two cannot be
installed apart. `plg_members_blog` requires both `hubzero/com_members` and
`hubzero/com_blog`, because it renders a `com_blog` archive on a `com_members`
profile page.

## The XML manifest

The XML manifest is what the CMS reads. It is named after the plugin —
`example.php` is described by `example.xml` — and it is not listed in its own
`<files>` block.

<!--include: core/plugins/content/formatwiki/formatwiki.xml:1-19-->

| Element or attribute | Meaning |
|---|---|
| `type="plugin"` | Required. |
| `group="content"` | Required. The directory the plugin installs into, and the `folder` column of its extension row. |
| `version` | The manifest format version, not the plugin's. |
| `<name>` | Shown in the Plugin Manager. Either a language key, as here, or a readable string like `Groups - Forum`. |
| `<description>` | A language key, resolved from the `.sys.ini`. |
| `<files>` | Files to install. The `plugin` attribute on one `<filename>` marks the entry point, and its value must be the plugin name. |
| `<languages>` | Translation files to copy into place. |
| `<config>` | The parameter form. See [Configuration](07-configuration.md). |

Subdirectories are declared with `<folder>`:

```xml
<files>
	<filename plugin="example">example.php</filename>
	<filename>index.html</filename>
	<folder>views</folder>
	<folder>assets</folder>
</files>
```

## Naming the plugin for humans

`<name>` is what an administrator sees in a list of dozens of plugins, so the
convention is `Group - Name`: `Groups - Forum`, `Members - Blog`,
`System - Test`. Using a language key instead, as `plg_content_formatwiki`
does, gets the same string out of the `.sys.ini` and makes it translatable.

## Installing

An administrator installs the package through **Extensions → Install**, or a
developer copies the directory into `app/plugins/{group}/` by hand. Either
way the plugin does nothing until its migration has run and written the
`#__extensions` row — see [Migrations](01-migrations.md) — because
`Hubzero\Plugin\Loader` builds its list from that table and never scans the
filesystem.

> **Note:** Registering a plugin is enough to make it run. Unlike a module,
> there is no second step: an enabled plugin whose access level the current
> user holds is imported with its group and bound to every event its public
> methods are named after.
