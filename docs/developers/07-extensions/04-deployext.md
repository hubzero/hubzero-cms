<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
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

Read the box below before you plan a release. It is the difference between an
afternoon and a wasted day.

> **Important:** There is no package installer. The **Install**, **Update**,
> **Discover** and **Database** screens were removed, and only some orphan
> language strings are left behind. You cannot upload a `.zip` and have it
> unpack itself, and any instruction that says otherwise, here or elsewhere,
> is describing software this release does not have. See the
> [Extension Manager](../../managers/10-extensions/04-extension-manager.md).

Two ways remain, and they are the two below. Both of them put a directory
where it belongs and stop; neither creates a table or a row. That second half
is always a [migration](../06-database.md#migrations).

Which to use:

- **The git flow** for anything a hub will keep and update — your own
  extensions, and anything you distribute. The hub records where the code came
  from and can fetch the next version.
- **By hand** while you are writing the extension, and for anything with no
  repository. It is faster to iterate on and leaves nothing behind that tells
  the next administrator where the code came from.

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
   create, prefix included, so `com_bookings` or `mod_bookings` — and the
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
   php core/bin/muse migration -e=com_bookings      # dry run: what would happen
   php core/bin/muse migration -e=com_bookings -f   # actually run it
   ```

   That runs the extension's own [migrations](../06-database.md#migrations),
   creating its tables and its `#__extensions` row. Without `-f` the runner
   only reports; `-e` restricts it to the one extension.

   `-e` matches on the migration **class name**, not the directory:
   `com_bookings` becomes `ComBookings` and is matched against
   `Migration{14 digits}ComBookings.php`. A file whose suffix does not match
   is skipped silently, and the dry run reports nothing to do — which looks
   exactly like an extension with no migrations. The same option rejects any
   name with a second underscore, because it validates against
   `^com_[[:alnum:]]+$`.

3. If you have changed an existing component's row rather than created one,
   clear the cache. `Hubzero\Component\Loader::load()` caches the
   `#__extensions` row under `_system.{option}` for `cachetime` minutes
   (15 by default), so a component you have just **enabled** can go on
   returning `JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND_OR_ENABLED` as a 404,
   and parameters you have just seeded can read stale, until the entry
   expires. A component registered for the first time is not affected: no row
   means nothing useful was cached.

An extension under `app/` completely replaces a core extension of the same
name. The loaders take the first directory they find, `app/` before `core/`,
and never mix the two. Copying half of a core component into `app/` to change
one file therefore breaks the other half — every class you did not copy stops
being found. To change a few files, use a
[template override](../11-templates/09-overrides.md).

### Registering without muse

If migrations are not an option, the row can be written directly. The
columns the loaders read are `type`, `element`, `folder`, `client_id` and
`enabled`; the rest have workable defaults.

```sql
INSERT INTO `#__extensions`
	(`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
VALUES
	('com_bookings', 'component', 'com_bookings', '', 0, 1, 1, 0, '', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);
```

A plugin's row carries its group in `folder` and its bare name in `element`
— `('Bookings - Notify', 'plugin', 'notify', 'bookings', 0, 1, ...)`.

Prefer the migration. It is versioned with the code, it reverses cleanly, it
creates the asset row and administrator menu entry that a bare `INSERT`
misses, and it is what will run on the next hub. Hand-written SQL is a hub
that works and a second hub that does not, with nothing written down to
explain the difference.

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

The Extension Manager's **Core Migrations** screen and the `muse` command are
what remain of the removed **Database** screen, and between them they are the
whole of the schema-management story in this release.

Migrations that belong to the platform rather than to one extension are run
from that screen, or on the command line:

```bash
php core/bin/muse migration -f
```

See [Migrations](../06-database.md#migrations) for what a migration can do and
how the runner decides what is pending.
