<!--
status: rewritten
reviewed-against: 2.4-main @ 348f0057c2
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/webdevs/muse
-->
# Muse

Muse is the hub's command line. It is the way anything runs against a hub
without a browser in front of it: migrations, cache clears, scheduled jobs,
bulk data work, packaging an extension, installing the hub in the first
place. Extensions add commands of their own, and that is how a component gets
work done on a timer or by hand.

The point of running through muse rather than a standalone PHP script is that
muse boots the application first. Inside a command you have the configuration,
the database, models, the language files, the event dispatcher and the
container — the same objects a controller has. A script in `app/bin` has none
of that until it bootstraps the framework itself, and usually bootstraps it
slightly wrong.

## When to write a command

Write a muse command when the work is part of an extension and someone will
run it more than once: a nightly job, a re-index, an import, a repair task
that support staff run when a user reports something. Ship it with the
extension, in the extension's own `commands` directory, so it travels with
the code that it operates on.

Write a plain script only for something genuinely throwaway that you will
delete the same day.

Do not write a command for a schema change. Schema changes are
[migrations](06-database.md#migrations); the runner tracks which have run and
an administrator applies them as part of an upgrade, which a command does not
give you.

## Running it

The executable is `core/bin/muse`, and it is not on the path. Run it from
the hub's root directory:

```bash
php core/bin/muse
```

With no arguments it prints the available commands. `php core/bin/muse help`
does the same, and `php core/bin/muse <command> help` describes one command.

Run it as a user that can read the hub's configuration and write its cache
and logs, usually the web server user. Running as root is refused unless you
confirm it.

> **Note:** Older documentation says to run `php muse` from the document
> root. There is no `muse` there; the file moved under `core/bin`.

## The commands

The [muse reference](../reference/muse.md) lists every command and task in the
framework, generated from the command classes, and [Common tasks](#common-tasks)
walks through the ones used most.

> **Important:** The reference is generated from
> `core/libraries/Hubzero/Console/Command/` only. Commands that ship inside a
> component are not in it, and are not in `muse help` either — see
> [Component commands](#component-commands) for why. To find them, look for a
> `commands` directory under `core/components/`.

Commands with a colon in the name are sub-commands, implemented in a
subdirectory: `muse cache:css` is `Command/Cache/Css.php`, and
`muse app:package` is `Command/App/Package.php`.

## How a command is found

Muse looks up a command by name in a list of registered namespaces. Three are
registered, and the first that yields a class wins:

| Namespace | Directory | Reached as |
|---|---|---|
| `\App\Commands` | `app/commands` | `muse <name>` |
| `\Components\{Name}\Commands` | `app/components/com_<name>/commands`, then `core/components/com_<name>/commands` | `muse <name>:<command>` |
| `Hubzero\Console\Command` | `core/libraries/Hubzero/Console/Command` | `muse <name>` |

The framework namespace is registered last, so a hub can shadow a framework
command by putting a class of the same name in `app/commands`. That is
deliberate and it is also a good way to break an upgrade; prefer a new name.

Within a namespace the file name is the command name, so `muse database` runs
`Command/Database.php`, which declares:

```php
namespace Hubzero\Console\Command;

class Database extends Base implements CommandInterface
{
}
```

A command must extend `Base` and implement
[`CommandInterface`](../../core/libraries/Hubzero/Console/Command/CommandInterface.php),
which requires two methods:

| Method | Called when |
|---|---|
| `execute()` | the command is run with no task: `muse database` |
| `help()` | the command is run with `help`, or `execute()` delegates to it |

Every other public method is a task, named as it is typed:
`muse database dump` runs `dump()`.

> **Note:** A class that does not implement `CommandInterface` is not a
> command, and muse reports `Unknown command` rather than telling you the
> class was found but rejected. If a command you have just written is not
> found, check the `implements` clause before anything else.

### Component commands

A component's commands live in `<component>/commands/` and are namespaced
`Components\<Name>\Commands`. They are reached with a colon — the component
name, then the command:

```bash
php core/bin/muse cron:jobs run
php core/bin/muse publications:bundle
```

Those are [`core/components/com_cron/commands/jobs.php`](../../core/components/com_cron/commands/jobs.php)
and [`core/components/com_publications/commands/bundle.php`](../../core/components/com_publications/commands/bundle.php).
The class is the file name in studly case, and the file itself is lower case:

```php
namespace Components\Bookings\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;

class Reminders extends Base implements CommandInterface
{
}
```

in `core/components/com_bookings/commands/reminders.php`, run as
`muse bookings:reminders`.

> **Warning:** `muse help` lists the framework commands and nothing else — it
> scans its own directory. A component command is invisible until someone
> knows its name, so document it in the component's own pages. The generated
> [muse reference](../reference/muse.md) has the same limit.

> **Note:** `muse bookings` on its own does not work. Without the second half
> the lookup resolves to the namespace rather than a class, and the command is
> reported as unknown. The colon form is not optional.

## Writing one

```php
namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

class Example extends Base implements CommandInterface
{
    /**
     * Default: with no task, show the help.
     *
     * @return  void
     */
    public function execute()
    {
        $this->help();
    }

    /**
     * @museDescription  Says hello, optionally to someone in particular
     *
     * @return  void
     */
    public function hello()
    {
        $name = $this->arguments->getOpt('name', 'world');

        $this->output->addLine('Hello, ' . $name);
    }

    public function help()
    {
        $this->output
             ->getHelpOutput()
             ->addOverview('An example command')
             ->addTasks($this)
             ->render();
    }
}
```

`$this->output` writes to the terminal and `$this->arguments` reads what was
typed. `getOpt($key, $default = false)` returns an option by name;
`getOpt(4)` and friends return positional words, which is how
`muse scaffolding create migration for jos_things` reads its arguments.

`addTasks($this)` builds the help by reflection over the public methods,
skipping the constructor, `execute()` and `help()`. A task with no
`@museDescription` is still listed, as `no description available`.

Run the example with:

```bash
php core/bin/muse example hello --name="Ada"
```

### Docblock tags

| Tag | Where | Effect |
|---|---|---|
| `@museDescription` | task | The one-line description muse prints beside the task, and the text the generated reference uses |
| `@museIgnoreHelp` | class | Hides the whole command from the `muse help` listing |
| `@museArgument` | task | Describes an option the task accepts |

> **Warning:** Only `@museDescription` on a task and `@museIgnoreHelp` on a
> class change what muse prints. `@museArgument` is read by the generator
> that builds the [muse reference](../reference/muse.md) and by nothing in the
> framework, so an option documented only with that tag never appears in
> `muse <command> help`. `@museIgnoreHelp` on a *task* does nothing at all —
> [`Output\Help::addTasks()`](../../core/libraries/Hubzero/Console/Output/Help.php)
> never looks for it, and the task stays in the listing. Only
> [`Command\Help`](../../core/libraries/Hubzero/Console/Command/Help.php),
> which builds the top-level listing, reads it, and only from the class
> docblock. That is how the `Scaffolding` sub-commands stay out of
> `muse help`.

## Configuration, hooks, and aliases

`muse configuration` stores settings muse itself uses, such as the name and
email the scaffolding generator puts in file headers. It also holds hooks,
which run a shell command at a named point, and aliases, which shorten a
command name. See [Common tasks](#common-tasks).

## Common tasks

The commands reached for most often, with the reasoning behind them. The
[muse reference](../reference/muse.md) lists every framework command and task,
generated from the source, and is the place to look for syntax and for
anything not covered here.

Run everything below from the hub's root directory.

### Cache

[`muse cache`](../reference/muse.md#muse-cache) clears the hub's cache files.
[`muse cache:css`](../reference/muse.md#muse-cache-css) clears only the
compiled CSS, which is what you want after changing a template's stylesheets
and finding the browser still serving the old ones.

```bash
php core/bin/muse cache clear
php core/bin/muse cache:css clear
```

### Configuration

[`muse configuration`](../reference/muse.md#muse-configuration) holds settings
muse itself uses. The scaffolding generator asks for your name and email the
first time and stores them here, so generated files carry a sensible header.

```bash
php core/bin/muse configuration set --user_name="Ada Lovelace"
php core/bin/muse configuration set --user_email=ada@example.org
```

It also stores hooks — shell commands run at a named point — and aliases,
which are shortcuts for a command name:

```bash
# fix permissions after updating the repository
php core/bin/muse configuration:hooks add repository.afterUpdate "chmod -R g+w /www/docroot"

# muse env  ->  muse environment
php core/bin/muse configuration:aliases add env environment
```

Aliases are resolved before the namespace search, so an alias can shadow a
real command name.

### Database

[`muse database`](../reference/muse.md#muse-database) exists for two jobs:
backups, and moving content backwards through a deployment chain.

The second is the interesting one. Copying a production database over a
development one takes the production configuration with it — hostnames, mail
settings, credentials — and breaks the development hub. `dump` and `load`
move only the parts that should travel.

```bash
# on production
php core/bin/muse database dump

# copy the file across, then on development
php core/bin/muse database load <filename>
```

### Environment

[`muse environment`](../reference/muse.md#muse-environment) prints the current
user and database. It is a one-line sanity check before running anything
destructive, and worth making a habit of.

### Extension

[`muse extension`](../reference/muse.md#muse-extension) adds, deletes,
installs, enables and disables rows in the extensions table. Run with no task
it prompts for what it needs, so there is no syntax to remember.

> **Warning:** On a hub that uses migrations — which is every hub that is not
> your laptop — this command is the wrong tool. A
> [migration](06-database.md#migrations) that calls `addComponentEntry()`
> records what it did and travels with the extension; `muse extension` changes
> one database and leaves no trace. Use it for local testing only.

### Group

The [`muse group`](../reference/muse.md#muse-group) tasks are wrappers on
existing commands, run in a super group's context and against its database.
See [Super groups](13-supergroups/README.md).

### Log

[`muse log follow`](../reference/muse.md#muse-log-follow) tails and filters a
log. Three log types are supported — `post`, `profile` and `sql` — and each
has to be enabled before anything appears in it.

```bash
php core/bin/muse log follow profile
```

It prints the field layout first, with an asterisk against each visible
field:

```
<0:*timestamp> <1:*hubname> <2:*ip> <3:*app> <4:*uri> <5:*query> <6:*memory> <7:*querycount> <8:*timeinqueries> <9:*totaltime>
```

Press a field's number to hide or show it, `f` to reprint the layout, and `h`
for the rest: `q` quit, `i` input mode, `p` pause, `b` beep, `r` re-render the
last hundred lines. Following the profile log while clicking through a page
is the quickest way to find the request that runs four hundred queries.

### Migration

See [Migrations](06-database.md#migrations) in the database chapter for
writing one, and
[`muse migration`](../reference/muse.md#muse-migration) for the command.

### Repository

[`muse repository`](../reference/muse.md#muse-repository) wraps whatever
mechanism manages this copy of the CMS. Git is the only one currently
supported; run it with no task to find out whether it applies to your
environment.

```bash
php core/bin/muse repository            # is this repository managed, and is it clean?
php core/bin/muse repository update     # what would the update bring?
php core/bin/muse repository update -f  # do it
```

As with migrations, the read-only form comes first and `-f` commits to it. A
failed update rolls back to the state before it started, and leaves you to
finish the update by hand.

[`muse repository clean`](../reference/muse.md#muse-repository-clean) prunes
rollback points and stashes, and asks before each.

### Scaffolding

[`muse scaffolding`](../reference/muse.md#muse-scaffolding) writes the files
you would otherwise copy from an existing extension and rename. It knows how
to create commands, components, migrations and tests.

```bash
php core/bin/muse scaffolding create component com_bookings
```

That writes a component skeleton under `core/components/com_bookings`: the
manifest, the site and admin entry points, a controller on each side, an
admin display template, site display and edit templates, a model,
`config/config.xml` and `config/access.xml`, the site and admin language
files, a router, and empty CSS and JS assets. The component name is
substituted throughout. It refuses to run if the directory already exists.

What it does not write is an API controller or a migration. Add the migration
yourself; see [Writing one](06-database.md#writing-one).

### Test

[`muse test`](../reference/muse.md#muse-test) is a wrapper around PHPUnit that
knows where each extension's tests live. `muse test show` lists the extensions
that have tests; `muse test run <extension>` runs one extension's. See
[Testing](15-testing.md).

### User

[`muse user`](../reference/muse.md#muse-user) merges and unmerges accounts.
People do create a second account by mistake and then ask for their
contributions to be moved, which means updating a user id across every table
that references one.

```bash
php core/bin/muse user merge 1042 into 1003
php core/bin/muse user unmerge 1042 from 1003
```

> **Important:** This command is experimental. It reports each table it
> touches and skips any where the change would violate an integrity
> constraint, which means a merge can be partial.

### Commands not covered above

`app` and its `package` and `repository` sub-commands manage Composer
packages for a hub. `install` performs a fresh installation, step by step.
`htmx` and `inertia` scaffold and lint front-end integrations. `resources`
exports the resource catalogue and reports git statistics. `searchmigration`
rebuilds the search index. Each is listed with its tasks in the
[muse reference](../reference/muse.md).
