<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/muse
-->
# Muse

Muse is the platform's command-line tool. It runs migrations, clears caches,
generates extension scaffolding, packages extensions, manages users and
groups, and installs a hub. Components can add commands of their own.

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

The [muse reference](../../reference/muse/README.md) lists every command and
task, generated from the command classes, and
[Common tasks](commands.md) walks through the ones used most.

Commands with a colon in the name are sub-commands, implemented in a
subdirectory: `muse cache:css` is `Command/Cache/Css.php`, and
`muse app:package` is `Command/App/Package.php`.

## How a command is found

Muse looks for commands in `core/libraries/Hubzero/Console/Command/`. The
file name is the command name, so `muse database` runs
`Command/Database.php`, which declares:

```php
namespace Hubzero\Console\Command;

class Database extends Base implements CommandInterface
{
}
```

A command must extend `Base` and implement `CommandInterface`, which
requires two methods:

| Method | Called when |
|---|---|
| `execute()` | the command is run with no task: `muse database` |
| `help()` | the command is run with `help`, or `execute()` delegates to it |

Every other public method is a task, named as it is typed:
`muse database dump` runs `dump()`.

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
typed. `addTasks($this)` builds the help from the docblocks, which is why
the `@museDescription` line matters: it is the one-line description muse
prints beside the task, and the generated reference reads the same tag.

Two other tags are recognised in a task's docblock:

| Tag | Effect |
|---|---|
| `@museDescription` | the task's one-line description |
| `@museArgument` | describes an option the task accepts |
| `@museIgnoreHelp` | hides the task from the help listing |

Run the example with:

```bash
php core/bin/muse example hello --name="Ada"
```

## Configuration, hooks, and aliases

`muse configuration` stores settings muse itself uses, such as the name and
email the scaffolding generator puts in file headers. It also holds hooks,
which run a shell command at a named point, and aliases, which shorten a
command name. See [Common tasks](commands.md).
