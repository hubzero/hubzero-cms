<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/invoke
source-id: 3537
modified: 2011-03-07
imported: 2026-09-09
-->
# Launching tools with invoke scripts

The invoke script is the handover point between the hub and your tool. The CMS
asks the middleware to start a session; the middleware runs your
`middleware/invoke`; your invoke script sets the container up and starts the
program.

> **Important:** Only the hub's half of that handover is in this repository.
> The first section below is CMS-side and was checked against `com_tools`. The
> rest describes `invoke_app`, the platform's own invocation script, which is
> installed on the execution hosts and is not in this repository. Its options
> are carried over from help.hubzero.org and could not be verified. Run
> `invoke_app -h` on your hub, or read the script itself, before relying on a
> detail here.

## What the CMS hands over

### The launch URL

A tool is launched at

```text
/tools/<alias>/invoke
/tools/<alias>/invoke/<version>
```

The third segment is a version selector, and the CMS turns it into the name of
an installed instance:

| Segment | Instance launched |
|---|---|
| omitted, `default`, `current`, `1` | The current published version |
| `test`, `dev` | `<alias>_dev`, the development version |
| a revision number | `<alias>_r<number>` |

`/tools/<alias>/reinvoke` restarts an existing session, and
`/tools/<alias>/session/<number>` shows a running one. A guest who follows a
launch URL is sent to sign in first.

Two query arguments matter to a tool developer:

- `params` — values to hand to the tool, described below.
- `return` — where to send the member when the session ends.

### Parameters in the URL

`params` carries a list of values, one per line, each `type:value` or
`type(name):value`, with `directory`, `file`, and `int` as the three types:

```text
directory:/home/hubname/username/mycase
file(input):/home/hubname/username/mycase/run.xml
int(runs):20
```

The CMS validates every line before it starts anything. Path values are
expanded from `~/`, required to be absolute, normalised, checked for control
characters, and then required to sit under one of the directories in the
component's **Directory Parameter Whitelist**. One bad line rejects the whole
launch, and the member gets the **Bad Parameters** page instead of a session.

If every line passes, the CMS forwards the `params` text **exactly as it
arrived**, URL-encoded — not the expanded and normalised form the check ran
on. Your tool therefore receives `~/` and `..` unresolved and has to expand
them itself, and it has to agree with the whitelist about what those resolve
to. The parameters stored with a session are re-sent when the session is
resumed, without being checked again.

[Directory parameter whitelist](../administrators.md#directory-parameter-whitelist)
documents the validation rule by rule and how an administrator sets the list.

### What the middleware is asked for

The CMS does not run your invoke script. It sends the middleware a `start`
command built from the member's username, their IP address, the instance name,
the version, the URL-encoded `params`, and, on a hub with zones, the zone the
member was assigned to. The middleware replies with a session number, which
the CMS stores and uses in the session URL.

The launch command the middleware runs is recorded per version by the CMS as

```text
<invoke script dir>/<toolname>/<version dir>/middleware/invoke -T <version dir>
```

with the version directory `dev` or `r<revision>`, and the invoke script
directory taken from the component's **Invoke Script Dir** option, `/apps` by
default. That is the whole of the CMS's knowledge of your invoke script: where
it lives, and that it is given `-T`. Everything else — the options below, the
window manager, the environment — is between your script and `invoke_app`.

*(All of the above verified against `com_tools`: the router, the session
controller, and the version records the pipeline writes.)*

## invoke_app

Everything from here on describes the tool platform and was not verified.

`invoke_app` is the hub's own invocation script, a bash script normally at
`/usr/bin/invoke_app`. It works out where the tool is installed, sets up the
environment and the window manager, starts any helper programs, and then
starts the tool. For nearly every tool, `middleware/invoke` is one call to it:

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C calc
```

`"$@"` passes on whatever the invoke script itself was given, which is how the
`-T` from the hub reaches `invoke_app`. `-t` names the tool and `-C` gives the
command that starts it.

A tool that needs the container set up in a way `invoke_app` cannot manage can
do that work in its own invoke script instead, but this is rare.

### Options

| Option | Purpose |
|---|---|
| `-A` | Arguments to pass on to the tool |
| `-c` | Command to run in the background before the tool starts |
| `-C` | Command that starts the tool |
| `-d` | Working directory. Defaults to the session directory |
| `-e` | Set an environment variable |
| `-f` | Do not set `FULLSCREEN` |
| `-n` | nanoWhim version |
| `-p` | Prepend to `PATH` |
| `-r` | Rappture version |
| `-S` | Do not run the command through the submit client |
| `-t` | Tool name |
| `-T` | Tool root directory |
| `-u` | Environment packages to load |
| `-w` | Window manager |

In the values of `-e` and `-p`, `${VERSION}` is replaced with `$TOOL_VERSION`
and `@tool` with `$TOOLDIR`.

### The options in detail

#### -A, arguments for the tool

Passed to the tool untouched; `invoke_app` does not read them.

```sh
-A "-q blah1 -w blah2"
```

#### -c, background command

Runs before the tool starts. Repeatable.

```sh
-c "echo hi" -c "filexfer"
```

#### -C, the command that starts the tool

The tool's own arguments can go here or in `-A`. `@tool` stands for the tool's
root directory, and the tool's `bin` directory is already on `PATH`, so a
program in `bin` can be named on its own:

```sh
-C @tool/bin/myprog
-C "@tool/bin/myprog -e val1 -b val2"
-C @tool/bin/myprog -A "-e val1 -b val2"
-C myprog
```

The shortcut works only when the program itself is the executable. Running a
script through an interpreter — `perl myscript.pl` — needs the full command,
because `perl` is the executable and the script is an argument.

#### -d, working directory

Changes to this directory before starting the tool. The default is the session
directory, `$SESSIONDIR`.

#### -e, environment variable

Sets the variable, replacing any previous value.

```sh
-e LD_LIBRARY_PATH=@tool/../${VERSION}/lib:${LD_LIBRARY_PATH}
```

#### -f, no full screen

Disables the `FULLSCREEN` environment variable, which Rappture uses to expand
its window to the whole screen.

#### -p, add to PATH

Prepends to `PATH` rather than replacing it. `@tool/bin` is added
automatically, so this is for extra directories.

```sh
-p @tool/../${VERSION}/bin
```

#### -r, Rappture version

Sets which Rappture the tool runs against. Left out, it defaults to `system`,
the version the default Rappture environment package points at. Set to `none`,
`invoke_app` skips looking for the `rappture`, `simsim`, and `about`
executables and disables them — which is what a non-Rappture tool wants:

```sh
-r none
```

On a hub with several Rappture versions installed, a tool can also pick one by
putting the directory holding its `rappture` executable on `PATH`.

#### -S, no submit

Takes no argument. By default `invoke_app` runs the `-C` command through the
`submit` client, except for the commands `rappture`, `simsim`,
`getrappturexml`, and `nanowhim`. `-S` adds your command to that list, so it
runs locally. It is a debugging flag.

#### -t, tool name

The tool's short name, the same one used for the source repository and the
tool alias. It decides where `invoke_app` looks for `tool.xml` and for the
`bin` directory — `/apps/<toolname>/<version>/...`.

#### -T, tool root directory

The directory holding the checked-out or installed tool: the one with `src`,
`bin`, `middleware`, `rappture`, `doc`, `data`, and `examples` under it. The
hub supplies this when it launches a session. Supply it yourself when testing
from a working copy:

```console
$ ./middleware/invoke -T $PWD
```

`"$@"` in the invoke script is what forwards it to `invoke_app`.

#### -u, environment packages

Names `use` scripts to source before the tool runs, from the hub's
`/apps/environ` directory. Repeatable.

```sh
-u octave-3.2.4 -u petsc-3.1-real-gnu
```

Package names and versions differ from hub to hub. Look in `/apps/environ` on
your own hub rather than copying a version from documentation.

#### -w, window manager

One of `headless`, `ratpoison`, `captive`, or `icewm`. The default is
`ratpoison` where it is installed, otherwise the `icewm` captive setup. Use
`headless` for a tool that needs no window manager at all, such as a Jupyter
tool. Where several are given, the first wins.

```sh
-w captive
-w headless
```

## Examples

### A Jupyter notebook tool

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C "start_jupyter -t -A -T @tool/bin calc.ipynb" \
                         -u anaconda-X \
                         -r none \
                         -w headless
```

`-C` starts the notebook through `start_jupyter`; `-r none` says the tool does
not use Rappture; `-w headless` says it needs no window manager, since the
notebook is served to the browser rather than drawn on an X display.
`anaconda-X` stands for whichever Anaconda environment package your hub
provides. [Jupyter notebooks](10-jupyter-notebooks/README.md) covers
`start_jupyter` and its display modes.

### A Linux GUI tool

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C calc \
                         -c filexfer \
                         -w captive
```

Here `calc` is a GUI built with something other than Rappture — PyQt, MATLAB,
or anything else. `-c filexfer` starts the file transfer helper before the GUI,
so the member can move files in and out of the session. `-w captive` uses the
icewm captive window manager, which suits a tool that opens more than one
window; a single-window tool can leave the option out and get the default.

Without the file transfer helper, the same tool is two lines of options:

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C calc
```

### A Rappture tool

> **Note:** [Rappture is deprecated](02-overview.md#rappture). This is here for
> the tools that already use it.

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C rappture
```

`-C rappture` starts the Rappture interface, which then looks for
`${TOOLDIR}/rappture/tool.xml` and stops with an error if it is not there.
The same two lines cover a Rappture tool whatever the solver is written in —
MATLAB, Python, Java, Fortran — because Rappture, not the solver, is what
`invoke_app` starts.

`-T` can be added to say where the tool root is, though the hub normally
supplies it:

```sh
/usr/bin/invoke_app "$@" -t calc \
                         -C rappture \
                         -T ${PWD}
```

### Extra environment and arguments

Load an environment package before starting the tool:

```sh
#!/bin/sh

/usr/bin/invoke_app "$@" -t calc \
                         -C calc \
                         -u octave-3.2.4
```

Pass arguments to the tool, either through `-A` or inside `-C`:

```sh
/usr/bin/invoke_app "$@" -t calc -C calc -A "-value 13 -value 5 -op add"
/usr/bin/invoke_app "$@" -t calc -C "calc -value 13 -value 5 -op add"
```

## See also

- [Tool repository structure](01-toolrepostructure.md) — where the invoke
  script lives and what the hub seeds it with.
- [Passing path variables with the invoke script](07-toolpaths/02-toolpathsinvoke.md) —
  getting `@tool` and other paths through to the tool.
- [Invoke scripts for Jupyter notebooks](10-jupyter-notebooks/05-invoke-jupyter.md) —
  `start_jupyter` and the notebook, App, and Tool display modes.
- [Directory parameter whitelist](../administrators.md#directory-parameter-whitelist) —
  what the CMS accepts in `params`.
