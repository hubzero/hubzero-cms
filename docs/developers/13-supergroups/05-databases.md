<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/databases
source-id: 3524
modified: 2014-09-10
-->
# Databases

A super group can have a database of its own, separate from the hub's. It is
named after the group's alias, its credentials live in the group directory,
and any code the group runs can open it.

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
in the managers book.

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
		'database' => 'sg_mygroup',
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

> **Note:** `prefix` is empty, so there is no `sg_mygroup` table prefix to
> think about. `#__` in a query is replaced with the empty string: write
> `#__mytable` or `mytable`, whichever you find clearer, and expect the table
> to be called `mytable`.

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
$database = \Hubzero\User\Group\Helper::getDbo(array(), 'mygroup');
```

The hub's own database is a separate object and stays available:

```php
$hub   = \App::get('db');
$group = \Hubzero\User\Group\Helper::getDbo();
```

Keep them in two variables and use whichever you mean. `JFactory::getDBO()`,
which older documentation used for the hub connection, no longer exists.

> **Warning:** `getDbo()` never fails loudly. If the group is not a super
> group, or `config/db.php` is missing, or the `cn` variable is not set on
> the request, it returns **the hub's database** instead. Code written against
> it will then create its tables in the hub schema without complaining. Check
> the connection before you trust it — for example by comparing
> `$database->getPrefix()` with `\App::get('db')->getPrefix()`, or by asking
> for a table you know belongs to the group.

## Using it

The driver is the same one the rest of the CMS uses, so everything in
[Database](../06-database/README.md) applies:

```php
$database = \Hubzero\User\Group\Helper::getDbo();

$database->setQuery("SELECT * FROM `#__widgets` WHERE `state` = 1");
$rows = $database->loadObjectList();
```

Inside a [super group component](07-components.md) you usually do not need to
ask. A model extending `Hubzero\Base\Model` whose file lives under the
component directory gets the group connection automatically —
`initDbo()` checks whether the class file sits below `JPATH_GROUPCOMPONENT`
and returns the group database if it does.

Create the tables with a [migration](06-migrations.md) rather than by hand, so
the schema travels with the code.
