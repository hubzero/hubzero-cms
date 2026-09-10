<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid/pegasuswf
source-id: 3541
modified: 2012-09-20
imported: 2026-09-09
-->
# Pegasus workflow submission

`submit` can run a workflow through
[Pegasus](https://pegasus.isi.edu). Two cases are supported: workflows
`submit` generates for you from a parameter sweep, and workflows you build
yourself. In both, `submit` configures access to the computational
resources, so you do not have to supply a site catalog.

> **Note:** `submit` and its Pegasus integration belong to the tool
> execution platform, which is separate software and is **not in this
> repository**. Nothing here could be checked against the code, and the
> Pegasus release a hub runs could not be established either. Pegasus has
> changed how workflows are described and planned since this material was
> written, so check `pegasus-plan --help` and the Pegasus documentation for
> your hub's version before writing a workflow against the syntax below.

## Parameter sweeps

The `-p`/`--parameters` and `-d`/`--data` options describe a sweep
compactly, so you do not have to generate the whole set of input files and
command lines yourself. You declare substitutable parameters on the
`submit` command line, and `submit` substitutes their values into data
files and command arguments, covering every combination.

Each combination is a node in a workflow and a job on the chosen resource.
A curses interface shows the run's progress. The syntax is described under
[parameter sweeps](01-submitcmd.md#parameter-sweeps).

## Workflows you build yourself

A sweep is a workflow of independent nodes: no data passes between them.
When that is not enough, build the workflow yourself and wrap an
application around it, letting the user supply the values that matter.

Generate the abstract workflow description with one of the
[Pegasus APIs](https://pegasus.isi.edu), then hand the resulting file to
`submit`:

```text
submit pegasus-plan --dax daxFile
```

> **Warning:** `--dax` names the XML workflow description used by the
> Pegasus releases this page was written for. Later Pegasus releases
> changed both the workflow format and this option. Confirm what your
> hub's `pegasus-plan` accepts rather than copying the line as it stands.

Where more than one venue can run Pegasus workflows, name the one you want;
otherwise `submit` picks one at random.

```text
submit -v venueName pegasus-plan --dax daxFile
```

`submit --help venues` lists the venues you may use. `submit` supplies
several other `pegasus-plan` options itself, and reserves the right to
ignore options you pass silently.

A workflow can also run in the tool session itself, with `-l`/`--local`:

```text
submit --local pegasus-plan --dax daxFile
```

The hub's `use` command puts `pegasus-plan` and the other Pegasus commands
on your `PATH`, and sets the other environment variables the Python and
Java workflow APIs need.
