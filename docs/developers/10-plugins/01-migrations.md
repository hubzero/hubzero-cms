<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/plugins/migrations
-->
# Migrations

A plugin does nothing until it has a row in `#__extensions`. Dropping the
files into `core/plugins` or `app/plugins` is not enough:
[`Hubzero\Plugin\Loader::all()`](../../../core/libraries/Hubzero/Plugin/Loader.php)
builds its list from that table, not from the filesystem, so an unregistered
plugin is never required, never constructed, and never bound to an event.

Registration is done by a migration in the plugin's own `migrations`
directory, run by [`muse migration`](../12-muse/README.md).

## Where migrations live

```
core/plugins/members/blog/
    migrations/
        Migration20170831000000PlgMembersBlog.php
```

The class name is `Migration{timestamp}Plg{Group}{Name}`, and the file is
named after the class. Migrations run in timestamp order across every
extension on the hub, so give a plugin that depends on a component's tables a
later timestamp than the migration that creates them.

> **Note:** See [Migrations](../06-database/02-migrations.md) for naming
> conventions, the `muse` commands, and the schema helpers on `$this->db`.

## The registration migration

<!--include: core/plugins/members/blog/migrations/Migration20170831000000PlgMembersBlog.php-->

`addPluginEntry()` and `deletePluginEntry()` are macros resolved by
[`Hubzero\Content\Migration\Base`](../../../core/libraries/Hubzero/Content/Migration/Base.php).
The plugin-related ones are:

| Macro | Signature |
|---|---|
| `addPluginEntry` | `($folder, $element, $enabled = 1, $params = '')` |
| `deletePluginEntry` | `($folder, $element = null)` |
| `enablePlugin` | `($folder, $element)` |
| `disablePlugin` | `($folder, $element)` |
| `renamePluginEntry` | `($folder, $element, $name)` |
| `savePluginParams` | `($folder, $element, $params)` |

`$folder` is the group directory — `members`, `content`, `system` — and
`$element` is the plugin's own directory name. Neither carries a prefix; the
`plg_members_blog` form is assembled where it is needed.

> **Note:** `deletePluginEntry('members')` with no element removes **every**
> plugin in the group. Always pass the element unless that is genuinely what
> you want.

## Shipping default parameters

`addPluginEntry()` takes a fourth argument, a JSON string stored in the row's
`params` column. Use it when a plugin must arrive with a value other than the
manifest default:

```php
public function up()
{
    $this->addPluginEntry('content', 'formatwiki', 1, '{"applyFormat":"0","convertFormat":"1"}');
}
```

For a plugin that is already installed, `savePluginParams()` merges into the
existing row instead, which is the right tool when a later migration adds a
field:

```php
public function up()
{
    $params = $this->getParams('plg_content_formatwiki');
    $params->set('convertFormat', 0);

    $this->savePluginParams('content', 'formatwiki', $params);
}
```

## Installing disabled

Pass `0` as the third argument to register a plugin without switching it on.
This is the polite default for anything that changes site behaviour on sight —
an authentication provider, a content filter — leaving an administrator to
enable it once it is configured:

```php
$this->addPluginEntry('authentication', 'orcid', 0);
```

## Ordering

Plugins in a group run in the order given by the `ordering` column, ascending;
`Loader::all()` sorts on it and the dispatcher preserves insertion order for
listeners of equal priority. `addPluginEntry()` does not set an ordering, so a
new plugin lands wherever the default puts it. If your plugin must run before
or after a specific sibling — a content filter that has to see raw text, for
instance — set the column explicitly in the migration and say why in a
comment.

## Tables

Create any tables the plugin needs in the same migration, and write the
matching `down()`:

```php
public function up()
{
    if (!$this->db->tableExists('#__example_log'))
    {
        $this->db->query("CREATE TABLE `#__example_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `created` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    $this->addPluginEntry('system', 'example');
}
```

Most core plugins reverse only the registration in `down()` and leave the
schema alone, because dropping a table throws away data that a re-run cannot
restore.

## Running them

```bash
muse migration          # dry run
muse migration -f       # apply
muse migration -f -i    # apply, including migrations dated before the last run
```
