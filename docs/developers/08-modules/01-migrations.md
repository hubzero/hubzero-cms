<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/modules/migrations
-->
# Migrations

A module has to be registered in the `#__extensions` table before the CMS will
list it or let an administrator create an instance of it. That registration is
done by a migration: a small PHP class in the module's `migrations` directory
that the [`muse migration`](../12-muse.md) command runs.

## Where migrations live

```
core/modules/mod_mygroups/
    migrations/
        Migration20190109000000ModMyGroups.php
```

The class name carries the timestamp and the extension, and the file is named
after the class. Migrations are run in timestamp order across every extension
on the hub, so a module that depends on a component's tables should carry a
later timestamp than the migration that creates them.

> **Note:** See [Migrations](../06-database.md#migrations) for naming
> conventions, the `muse` commands, and the helpers available on `$this->db`.

## The registration migration

Most modules need exactly one migration, and it is four lines long:

<!--include: core/modules/mod_mygroups/migrations/Migration20190109000000ModMyGroups.php-->

`addModuleEntry()` and `deleteModuleEntry()` are macros resolved by
[`Hubzero\Content\Migration\Base`](../../../core/libraries/Hubzero/Content/Migration/Base.php)
from the `Macros` directory beside it. Their signatures are:

| Macro | Signature |
|---|---|
| `addModuleEntry` | `($element, $enabled = 1, $params = '', $client = 0)` |
| `deleteModuleEntry` | `($element, $client = null)` |
| `enableModule` | `($element)` |
| `disableModule` | `($element)` |
| `installModule` | `($module, $position, $always = true, $params = '', $client = 0, $menus = 0)` |
| `saveParams` | `($element, $params)` |
| `getParams` | `($element, $returnRaw = false)` |

`$element` is the full directory name, including the `mod_` prefix. `$client`
is `0` for a site module and `1` for an administrator module, and must agree
with `client="site"` or `client="administrator"` in the XML manifest, or the
module will never appear in the client you built it for.

> **Note:** `addModuleEntry` checks for an existing row first, so re-running a
> migration does not create a duplicate registration.

## Creating an instance from a migration

Registering a module does not place it anywhere. If your module is meant to
appear as soon as it is installed — a footer, a set of admin quick icons — use
`installModule()`, which writes both the `#__extensions` row and a published
`#__modules` instance assigned to a position:

```php
public function up()
{
    // Registers mod_quickicon and places an instance in the "icon" position
    $this->installModule('quickicon', 'icon', true, '', 1);
}
```

Note that `installModule()` takes the name *without* the `mod_` prefix and
adds it itself, while `addModuleEntry()` takes the prefixed name. The
`$always` argument controls the `#__modules_menu` row: `true` assigns the
instance to all menu items, `false` restricts it to the ids in `$menus`.

## Tables and schema

A module that needs its own tables creates them in the same migration, using
the raw query helpers described in the
[database migrations](../06-database.md#migrations) chapter:

```php
public function up()
{
    if (!$this->db->tableExists('#__example_items'))
    {
        $this->db->query("CREATE TABLE `#__example_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    $this->addModuleEntry('mod_example');
}
```

Write the matching `down()` so the change can be rolled back. Dropping a table
in `down()` throws away data, so most core modules only reverse the extension
registration and leave the schema alone.

## Running them

```bash
muse migration          # dry run: report what would change
muse migration -f       # apply
muse migration -f -i    # apply, including migrations dated before the last run
```

Until the migration has been applied, `Hubzero\Module\Loader::all()` will not
return the module — its query joins `#__modules` to `#__extensions` and
requires `e.enabled = 1` — so an unregistered module renders nothing even if
every file is in place.
