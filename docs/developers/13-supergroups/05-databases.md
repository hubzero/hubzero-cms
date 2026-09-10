<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/databases
source-id: 3524
modified: 2014-09-10
-->
# Databases

A super group can have a database of its own, separate from the hub's. It is
named after the group's alias, its credentials live in the group directory,
and any code the group runs can open it.

Use it for data that belongs to the group and to nothing else — the Coastal
Resilience Center's tide-gauge readings, arriving hourly from instruments the
hub knows nothing about. Two reasons to keep it out of the hub schema: the
hub's migrations and upgrades never have to know the table exists, and the
group's own developers can be given credentials to their database without
being given the hub's.

Data the hub already models — pages, members, files, citations, resources —
does not belong here. Store those as hub records and let the group's code read
them through the existing models.

## How the group gets one

Both halves are done when an administrator saves the group as a super group,
in [`_handleSuperGroup()`](../../../core/components/com_groups/admin/controllers/manage.php):

- A database named `sg_<alias>` is created, but **only if** the hub's own
  database account holds a grant matching `sg\_%`. If it does not, the save
  warns and carries on without a database.
- `config/db.php` is written from `/etc/supergroup.conf`, which holds the
  username and password of the account the hub gives every super group. If
  that file is missing, the save warns and no `config/db.php` is written.

Neither step overwrites an existing file, so re-saving the group is safe.

Both of those are administrator territory; see
[Super Groups](../../managers/06-users/08-supergroups.md#creating-a-super-group)
in the managers book. A developer's first move on a new super group is to
check that `config/db.php` exists, because a save that failed either step
still produced a working group and only a warning on screen.

## The config file

```
app/site/groups/<gidNumber>/config/db.php
```

It returns an array, and the hub writes it in this shape:

```php
<?php
	return array(
		'host'     => 'localhost',
		'port'     => '',
		'user'     => 'sgmanager',
		'password' => 'xxxxx',
		'database' => 'sg_coastal',
		'prefix'   => ''
	);
```

The array is handed straight to
[`Hubzero\Database\Driver::getInstance()`](../../../core/libraries/Hubzero/Database/Driver.php),
so any key that driver understands works here — `driver` to pick something
other than MySQL, `ssl_ca` for a TLS connection, `dsn` to write the connection
string yourself.

`config/db.php` is excluded from the group's git repository by
`gitlab_setup.sh`, along with `uploads/*`. Do not add it back.

> **Note:** `prefix` is empty, so there is no `sg_coastal` table prefix to
> think about. `#__` in a query is replaced with the empty string: write
> `#__gauges` or `gauges`, whichever you find clearer, and expect the table
> to be called `gauges`.

> **Caution:** The MySQL driver appends `port` to the connection string
> whenever the key is *set*, not whenever it has a value, so the empty
> `'port' => ''` the hub writes ends up in the DSN. If the connection fails
> with a port error, delete the line.

## Opening it

```php
$database = \Hubzero\User\Group\Helper::getDbo();
```

[`getDbo()`](../../../core/libraries/Hubzero/User/Group/Helper.php) takes the
group from the `cn` request variable, checks that it is a super group, reads
`config/db.php` and returns the driver. Instances are cached by their options,
so calling it repeatedly costs nothing.

Outside a group request — in a command line script, say — pass the alias as
the second argument, or the configuration array as the first:

```php
$database = \Hubzero\User\Group\Helper::getDbo(array(), 'coastal');
```

The hub's own database is a separate object and stays available:

```php
$hub   = \App::get('db');
$group = \Hubzero\User\Group\Helper::getDbo();
```

Keep them in two variables and use whichever you mean. Older documentation
reached for the hub connection through a legacy factory call that no longer
exists in this tree; `App::get('db')` is the current way and the only one.

> **Warning:** `getDbo()` never fails loudly. If the group is not a super
> group, or `config/db.php` is missing, or the `cn` variable is not set on
> the request, it returns **the hub's database** instead. Code written against
> it will then create its tables in the hub schema without complaining. Check
> the connection before you trust it — for example by comparing
> `$database->getPrefix()` with `\App::get('db')->getPrefix()`, or by asking
> for a table you know belongs to the group.

That warning is the single most expensive thing on this page. The failure
looks like success: the query runs, the page renders, and a `gauges` table
appears in the hub schema where the next hub upgrade will not expect it. A
command line script is the usual place it happens, because there is no `cn`
on the request.

## Using it

The driver is the same one the rest of the CMS uses, so everything in
[Database](../06-database.md) applies:

```php
$database = \Hubzero\User\Group\Helper::getDbo();

$database->setQuery("SELECT * FROM `#__gauges` WHERE `state` = 1");
$rows = $database->loadObjectList();
```

Create the tables with a [migration](06-migrations.md) rather than by hand, so
the schema travels with the code.

### Models and the group connection

Which base class a model extends decides which database it talks to, and the
two answers are opposite.

| Base class | Connection inside a super group component |
|---|---|
| `Hubzero\Base\Model` (the older base model) | the **group's**, automatically |
| `Hubzero\Database\Relational` (current) | the **hub's** |

[`Hubzero\Base\Model::initDbo()`](../../../core/libraries/Hubzero/Base/Model.php)
checks whether the class file sits below `JPATH_GROUPCOMPONENT` and returns
the group database if it does. That is the old base class, kept for the
components already written against it.

[`Relational`](../../../core/libraries/Hubzero/Database/Relational.php), the
model layer everything new should use, has no such check: its query object
defaults to `App::get('db')`. It can be pointed elsewhere, but only with
`Relational::setDefaultConnection()`, which is **static and global to the
request** — set it and every `Relational` model in that request, including the
hub's own, follows it. Do not do it in a super group component.

So in a group component either accept the older base class for models that
must reach the group database, or use `Relational` for hub records and the
driver directly, as above, for group tables. Mixing them in one model is where
this goes wrong quietly.
