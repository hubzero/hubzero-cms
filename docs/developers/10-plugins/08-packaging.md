<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/plugins/packaging
-->
# Packaging

A plugin ships as its own directory, laid out exactly as it will sit on disk.
Two manifests describe it: `{name}.xml`, which the CMS reads for the display
name and the parameter form, and `composer.json`, which describes the package
for a distribution mechanism this release does not itself run.

Read the next section before you plan a release, because what a plugin
"package" is here is probably not what you expect.

## There is no package installer

The **Install**, **Update**, **Discover** and **Database** tabs that the older
documentation names do not exist in this release; only orphan language strings
remain. You cannot upload a `.zip` and have it unpack itself. See the
[extension manager](../../managers/10-extensions/04-extension-manager.md) for
what the screen actually offers.

A plugin therefore arrives one of two ways:

- **The git-backed Custom Extensions flow.** The Extension Manager clones a
  repository into `app/` and updates it by fetching and merging. For a plugin,
  set the type to `plugin`, the alias to the plugin's own name — `notify`, not
  `plg_bookings_notify` — and **Folder (Plugins Only)** to the group, so the
  clone lands in `app/plugins/bookings/notify`. That folder value has to match
  the `folder` column your migration writes and the group prefix on the events
  you answer; see
  [Group and directory must match](02-structure.md#group-and-directory-must-match).
- **By hand.** Copy the directory into `app/plugins/{group}/` yourself. This is
  what you do while writing the plugin.

Either way the plugin does nothing until its migration has run and written the
`#__extensions` row — see [Migrations](01-migrations.md) — because
`Hubzero\Plugin\Loader` builds its list from that table and never scans the
filesystem. A hand copy does not run the migration; you do.

The git flow does run it, by shelling out to
`muse migration -d=up -f -i -r={the extension's directory}` — after the first
clone, and again after **Merge Code** has brought new commits into the working
tree. So a plugin installed that way registers itself, and a later release
that adds a migration applies it at the merge.

Full details of both routes are in
[Deploying extensions](../07-extensions/04-deployext.md).

> **Note:** Registering a plugin is enough to make it run. Unlike a module,
> there is no second step: an enabled plugin whose access level the current
> user holds is imported with its group and bound to every event its public
> methods are named after. There is nothing to publish and no position to
> choose.

## Package layout

```
plg_bookings_notify/
    language/en-GB/en-GB.plg_bookings_notify.ini
    language/en-GB/en-GB.plg_bookings_notify.sys.ini
    migrations/Migration20260210000000PlgBookingsNotify.php
    views/email/tmpl/message.php
    composer.json
    index.html
    notify.php
    notify.xml
```

The package directory is conventionally named `plg_{group}_{name}`, but what
lands on disk is `plugins/{group}/{name}` — the group is not a directory
inside the package, it is where the package is installed to. A repository used
with the Custom Extensions flow holds the *contents* of that directory at its
top level, with `notify.xml` beside `notify.php`, not a `plg_bookings_notify`
wrapper directory.

## The XML manifest

The XML manifest is the one the CMS reads. It is named after the plugin —
`notify.php` is described by `notify.xml` — and it is not listed in its own
`<files>` block.

<!--include: core/plugins/content/formatwiki/formatwiki.xml:1-19-->

| Element or attribute | Meaning |
|---|---|
| `type="plugin"` | Required. |
| `group="content"` | Required. The directory the plugin installs into, and the `folder` column of its extension row. |
| `version` | The manifest format version, not the plugin's. |
| `<name>` | Shown in the Plugin Manager. Either a language key, as here, or a readable string like `Bookings - Notify`. |
| `<description>` | A language key, resolved from the `.sys.ini`. |
| `<files>` | Files to install. The `plugin` attribute on one `<filename>` marks the entry point, and its value must be the plugin name. |
| `<languages>` | Translation files to copy into place. |
| `<config>` | The parameter form. See [Configuration](07-configuration.md). |

Subdirectories are declared with `<folder>`:

```xml
<files>
	<filename plugin="notify">notify.php</filename>
	<filename>index.html</filename>
	<folder>views</folder>
	<folder>language</folder>
</files>
```

## Naming the plugin for humans

`<name>` is what an administrator sees in a list of dozens of plugins, so the
convention is `Group - Name`: `Bookings - Notify`, `Groups - Forum`,
`Members - Blog`. Using a language key instead, as `plg_content_formatwiki`
does, gets the same string out of the `.sys.ini` and makes it translatable.
Prefer the key.

> **Warning:** The Plugin Manager renders `Lang::txt()` against the value
> *stored in the extension row at registration*, not against the manifest as
> it stands today. Changing `<name>` in the XML after a hub has installed the
> plugin changes nothing on that hub. Two shipped plugins carry the same
> literal name for this reason.

## The Composer manifest

```json
{
	"name": "myorg/plg_bookings_notify",
	"description": "Emails the lab manager when an instrument is reserved",
	"type": "hubzero-plugin",
	"keywords": ["hubzero"],
	"homepage": "https://example.org",
	"license": "MIT",
	"require": {
		"php": "^5.4",
		"hubzero/com_bookings": "dev-master"
	},
	"extra": {
		"install-directory": "/plugins/bookings/notify/"
	}
}
```

The `type` must be `hubzero-plugin`; the recognised types are
`hubzero-component`, `hubzero-module`, `hubzero-plugin`, and
`hubzero-template`.

> **Important:** `extra.install-directory` is required for a plugin and only
> for a plugin. Every other extension type can be placed from its package name
> alone, but a plugin's group cannot be inferred from `plg_bookings_notify` —
> the group and the element are both in there, with nothing to say where one
> ends.

> **Note:** No code in this release reads `type` or `extra.install-directory`.
> The Composer installer that consumed them is not part of the repository, and
> neither the Custom Extensions flow nor a hand copy looks at `composer.json`
> at all. Every shipped plugin still carries the file, and writing one keeps
> your package correct for whatever consumes it; just do not expect it to
> place anything. What actually decides where the code goes is the **Folder**
> field on the Custom Extensions form, or your own `cp`.

A plugin that extends a component should still declare the dependency, so that
a reader can see it. `plg_members_blog` requires both `hubzero/com_members`
and `hubzero/com_blog`, because it renders a `com_blog` archive on a
`com_members` profile page; `plg_bookings_notify` requires `com_bookings`,
because without the component nothing ever triggers its event.
