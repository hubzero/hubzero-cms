<!--
status: rewritten
reviewed-against: 2.4-main @ 91d03d0a23
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/supergroups/migrations
source-id: 3525
modified: 2014-09-10
-->
# Migrations

A super group's [database](05-databases.md) is versioned the same way the
hub's is: with migration classes that muse runs in order and records so it
never runs one twice. Putting schema changes in migrations means nobody has to
connect to the live database by hand, and means the schema travels with the
code — the admin screen that pulls a group's code runs its migrations
straight afterwards.

## The same runner, pointed somewhere else

There is no separate group migration system. `muse group migrate` reaches
[`Hubzero\Content\Migration`](../../../core/libraries/Hubzero/Content/Migration.php),
the same class `muse migration` runs against the hub, with two of its
constructor arguments filled in:

| Argument | For the hub | For a group |
|---|---|---|
| `$docroot` | omitted, so the runner walks `core/` and `app/` and every extension under them | the group's directory, which **replaces** the search path entirely |
| `$runDb` | omitted, so migrations run against the hub connection | the group's connection, from `config/db.php` |

Everything else — the file naming, the class shape, `up()` and `down()`, the
dry run, the `#__migrations` log — is the hub's machinery unchanged. Read
[Migrations](../06-database.md#migrations) first; this chapter is only what
the two substituted arguments change.

Both consequences follow from that table and are worth holding on to: only one
directory is searched, and the log row still lands in the **hub's** table even
though the schema change lands in the group's.

## Where they live

```
app/site/groups/<gidNumber>/migrations/
```

That one directory, and nothing under it. Because the group directory becomes
the entire search path, migrations inside the group's own
`components/com_*/migrations` are **not** found — the runner looks for
`<search path>/migrations` and stops. Keep them all in the group's top-level
`migrations` directory, and put the component name in the class name to tell
them apart.

The skeleton creates the directory when the group is first saved as a super
group.

## The class

Naming is as it is everywhere else: `Migration`, a fourteen-digit timestamp,
and the extension in studly case.

```
Migration20260901120000ComGauges.php
```

```php
<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for the tide gauge table
 **/
class Migration20260901120000ComGauges extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__gauges'))
		{
			$this->db->setQuery("CREATE TABLE `#__gauges` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL DEFAULT '',
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__gauges'))
		{
			$this->db->setQuery("DROP TABLE `#__gauges`");
			$this->db->query();
		}
	}
}
```

> **Warning:** A group migration class must **not** be namespaced. The runner
> works the expected namespace out from the file's path, and it only knows the
> `components`, `modules`, `plugins`, `templates` and `migrations` layouts — a
> path under `site/groups` gives it nothing, so it falls back to looking for a
> global class of the same name as the file. A namespaced class is skipped
> with *does not have a class of the same name* and the migration silently
> never runs.

`$this->db` inside the migration is the **group's** database, because muse
passes it as the alternate connection. `#__` expands to nothing there, since
the group's `prefix` is empty.

There is no supported way back to the hub's connection from inside a group
migration: `Base` keeps it in a private property and never offers it. If a
group migration genuinely has to touch the hub schema, ask for it explicitly
with `\App::get('db')` and be sure that is what you meant.

> **Note:** The extension macros — `addComponentEntry()`, `addPluginEntry()`,
> `addModuleEntry()` and the rest — are wired to `$this->db` too, which in a
> group migration is the group's database and not the hub's. They will try to
> write `#__extensions` inside the group schema. A super group component is
> not registered with the CMS in the first place, so there is nothing these
> should be used for here.

## Creating one

`muse group scaffolding migration` is documented in older material and does
not put the file in the right place on this release — it resolves its
install directory against `core/`, not `app/`. Give scaffolding an absolute
directory instead:

```bash
php core/bin/muse scaffolding create migration \
    -e=com_gauges \
    --install-dir=/path/to/hub/app/site/groups/1051
```

That writes `app/site/groups/1051/migrations/Migration<timestamp>ComGauges.php`
and opens it in `$EDITOR`. `-e` only decides the suffix on the class name;
add `-i` if the extension does not exist as a directory under the group and
scaffolding refuses it.

Writing the file by hand is equally valid. Nothing but the file name pattern
and the class name matters.

## Running them

```bash
php core/bin/muse group migrate --group=coastal -if
```

- `--group` takes the group's alias. Run muse from inside the group's
  directory and you may leave it off; the command works the group out from
  the current directory.
- `-i` ignores dates, so migrations the group may have missed are offered.
- `-f` makes it a real run. Without `-f` everything is a dry run that only
  lists what it would do — which is the safe way to look first.

`php core/bin/muse migration -f --group=coastal` is the same thing:
`group migrate` validates the group, sets the `group` option and hands over to
the migration command, which is where the work happens.

If the group has no `migrations` directory the command stops with *Error:
Migrations directory does not exist*, and if `config/db.php` is missing or
wrong it stops with *Error: Could not connect to Group Database*. Both are
loud, and both are much better than the silent failure the previous warning
describes, so a dry run that reports *nothing to migrate* on a directory you
know has files means the class is namespaced or misnamed.

## Where the runs are recorded

In the **hub's** `#__migrations` table, not the group's. The `scope` column
holds the path to the directory relative to the document root —
`app/site/groups/1051/migrations` — which is what keeps one group's history
separate from another's and from the hub's own.

So a group's database can be dropped and rebuilt, but the hub still believes
its migrations have run. Use `--force` with `--file=` to re-run one, or
`-d=down` first.

## Running them automatically

You only run migrations by hand in a development environment. On a hub whose
groups are managed through [GitLab](../14-supergroups-gitlab.md), an
administrator selecting **Merge Groups Code** runs, for each group,
`muse group update -f` followed by `muse group migrate -f`. New migrations
that arrive with a merge are applied as part of the merge.

> **Warning:** The controller means to skip the migration step when the update
> fails. It decides by looking for the word `ineligble` in the update output —
> misspelled, and in any case a word muse never prints. The test therefore
> always passes and migrations run even after a failed update. Read the
> output of a merge rather than trusting that it stopped itself. This is
> recorded in the review findings.
