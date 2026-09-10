<!--
status: rewritten
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/22/toolsnewdocs/prerequisites
source-id: 2847
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Prerequisites

What to know before you start writing a tool: where it runs, what it can
reach, and who does what to get it published.

> **Note:** The tool session and its environment belong to the tool platform,
> which is separate software from the CMS in this repository and could not be
> checked here. The pipeline that publishes a tool is CMS-side and is
> documented from the code.

- [Environment](#environment) — the container a tool session runs in and
  the environment variables your tool can rely on.

Two pages outside this section are worth reading first:

- [Tools](../../users/22-tools.md) in the users book says what a tool
  session looks like to the person running it.
- [Tools](../../managers/03-maintenance/02-tools.md) in the hub managers
  book documents the contribution pipeline, including every field on the
  registration form. [The contribution process](process.md) summarises the
  same pipeline from the developer's side.

You also need an account on the hub you are publishing to, and that account
has to be allowed to register a tool. Hubs are normally configured to let any
member register one.
## Environment

The container a tool session runs in, and the environment variables a tool
can rely on inside it.

> **Important:** The container, the variables, and the file system layout
> below belong to the tool platform, which is separate software from the CMS
> in this repository. They could not be verified here, and a hub can be built
> differently. Run `env` in a session on your own hub to see what it actually
> sets. The two CMS-side facts on this page are marked.

### The tool session

A tool session runs in a container on one of the hub's execution hosts. The
container runs as the member who launched the tool, with that member's
permissions, so the tool can read and write that member's files and nothing
else. Other members' sessions are not visible from inside it.

Because the tool runs as the member, anything it writes lands in their home
directory and counts against their disk quota.

If your hub gives you a workspace or `ssh` access, open a terminal in a
session and look around:

```console
$ pwd
/home/yourhub/yourname

$ echo $SESSION
19245

$ echo $SESSIONDIR
/home/yourhub/yourname/data/sessions/19245
```

The number in `$SESSION` is the session number the CMS shows in the session
URL, `/tools/<alias>/session/<number>`. The CMS gets it back from the
middleware when it starts the session and stores it; it is the handle both
sides use for the same session. *(CMS-side: `com_tools` session controller.)*

### Environment variables

A tool session sets a number of variables. The ones a tool normally cares
about:

| Variable | Value | What it is for |
|---|---|---|
| `SESSION` | The session number | Identifies the running session |
| `SESSIONDIR` | `$HOME/data/sessions/$SESSION` | A per-session directory, readable and writable by the tool and the member. The right place for temporary files. Delete them when the tool is done with them |
| `RESULTSDIR` | `$HOME/data/results/$SESSION` | Where simulation results go so the member can find them later. Rappture writes its XML output here |
| `USER` | The member's username | |
| `HOME` | `/home/<hub hostname>/<username>` | The member's home directory on the hub |
| `PWD` | The working directory the tool started in | `SESSIONDIR` unless the invoke script's `-d` option says otherwise |

Run `env` in a session terminal for the full list; it varies by hub and by
the environment packages an invoke script loads.

Two conventions worth following:

- Write temporary files to `$SESSIONDIR` and results to `$RESULTSDIR`, not to
  the home directory itself. Members are prompted to clear these directories
  when they run low on disk space, so files left there are not permanent, but
  they are also not in the member's way.
- If your tool saves work the member is meant to keep, give it a directory of
  its own — `$HOME/data/<toolname>` — rather than dropping files at the top of
  the home directory. Watch the member's quota while you do it.

The CMS holds each member's home directory in their profile and uses it in two
places: it expands a leading `~/` in a launch parameter against it before
checking the parameter against the
[directory parameter whitelist](../administrators.md#directory-parameter-whitelist),
and it reads the member's files for the session storage screens through the
component's `storagepath` setting, which defaults to `/webdav/home`.
*(CMS-side: `com_tools` session and storage controllers.)*

[Tool paths](07-toolpaths/README.md) covers the same variables in more
detail, along with how a tool finds its own installed directory and its
example data.
