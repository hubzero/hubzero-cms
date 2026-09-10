<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/extensions/deployext
source-id: 3470
modified: 2020-12-29
imported: 2026-09-09
-->
# Deploying extensions

Getting an extension onto a running hub is two separate things: putting the
code in the right directory, and writing the row in `#__extensions` that
makes the platform notice it. Nothing scans the filesystem, so the code
alone does nothing.

> **Important:** There is no package installer. The **Install**, **Update**,
> **Discover** and **Database** screens were removed, and only some orphan
> language strings are left behind. You cannot upload a `.zip` and have it
> unpack itself, and any instruction that says otherwise, here or elsewhere,
> is describing software this release does not have. See the
> [Extension Manager](../../managers/10-extensions/04-extension-manager.md).

Two ways remain, and they are the two below.

## From a git repository

The Extension Manager's **Custom Extensions** tab clones a git repository
into `app/` and updates it by fetching and merging. This is the supported
route for a hub's own extensions, and the one to use if the extension has a
repository.

1. Push the extension to a repository whose contents are laid out as the
   extension directory itself — the manifest at the top level, not inside a
   wrapper directory.
2. In the administrator interface, go to **Extensions** → **Extension
   Manager** → **Custom Extensions** and select **New**.
3. Fill in the HTTPS clone URL, a name, the alias — the directory name to
   create, prefix included, so `com_example` or `mod_example` — and the
   type. For a plugin, also set **Folder (Plugins Only)** to its group. A
   private repository needs a **GIT Personal Access Token**.
4. **Save & Close**, then tick the row and select **Update Selected Custom
   Extensions**. That is what actually fetches the code; saving the record
   does not.
5. Review the incoming commits and select **Merge Code**.

The type determines where the clone lands, under `PATH_APP`:

| Type | Installed to |
|---|---|
| `component` | `app/components/{alias}` |
| `module` | `app/modules/{alias}` |
| `plugin` | `app/plugins/{folder}/{alias}` |
| `template` | `app/templates/{alias}` |
| `language` | `app/languages/{alias}` |
| `library` | `app/libraries/{alias}` |
| `non-standard` | `app/{alias}`, contents as they are |

The full field-by-field description of the form is in the
[Extension Manager](../../managers/10-extensions/04-extension-manager.md) chapter.

## By hand

For development, and for anything without a repository, copy the directory
into place yourself.

1. Put the extension directory under `app/`, at the path its type requires —
   the table above gives them. Ownership and permissions must match the rest
   of `app/`, or the web server will not read it.
2. Register it. From the CMS root:

   ```bash
   php core/bin/muse migration -e=com_example      # dry run: what would happen
   php core/bin/muse migration -e=com_example -f   # actually run it
   ```

   That runs the extension's own [migrations](../06-database.md#migrations),
   creating its tables and its `#__extensions` row. Without `-f` the runner
   only reports; `-e` restricts it to the one extension.

3. Clear the cache if the extension is a component: `Hubzero\Component\Loader`
   caches the extension row for `cachetime` minutes, so a component
   registered a moment ago can still 404 until the cache expires.

An extension under `app/` completely replaces a core extension of the same
name. The loaders take the first directory they find, `app/` before `core/`,
and never mix the two.

### Registering without muse

If migrations are not an option, the row can be written directly. The
columns the loaders read are `type`, `element`, `folder`, `client_id` and
`enabled`; the rest have workable defaults.

```sql
INSERT INTO `#__extensions`
	(`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
VALUES
	('com_example', 'component', 'com_example', '', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);
```

A plugin's row carries its group in `folder` and its bare name in `element`
— `('System - Example', 'plugin', 'example', 'system', 0, 1, ...)`.

Prefer the migration. It is versioned with the code, it reverses cleanly,
and it is what an upgrade will run on the next hub.

## After the code is in place

- **Components** appear under **Components** in the administrator menu once
  the row exists and `enabled` is `1`. **Refresh Cache** in the Extension
  Manager re-reads the XML manifest if you have edited it.
- **Modules** need a second step. The row registers the module *type*; an
  instance is created in **Extensions** → **Module Manager** → **New**,
  where you pick the type, give it a title, choose a position and set its
  parameters. Nothing renders until an instance is published.
- **Plugins** need no second step. An enabled plugin is imported with its
  group and bound to every event its public methods are named after.
- **Templates** are assigned in **Extensions** → **Template Manager**, by
  selecting the template and making it the default for the site or the
  administrator interface.

## Core migrations

Migrations that belong to the platform rather than to one extension are run
from the Extension Manager's **Core Migrations** screen, or on the command
line:

```bash
php core/bin/muse migration -f
```

See [Migrations](../06-database.md#migrations) for what a migration can do and
how the runner decides what is pending.
