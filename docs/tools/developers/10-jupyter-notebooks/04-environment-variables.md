<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/environment-variables
source-id: 3561
modified: 2022-11-22
imported: 2026-09-09
-->
# Environment variables in a notebook

A notebook running as a tool is a tool session like any other, so the session
environment variables are there to be read. This page covers only what is
particular to reading them from a notebook, and where a notebook should write
its files.

> **Important:** Session environment variables are set by the tool execution
> platform, which is separate software and is **not in this repository**.
> Nothing here could be checked against code in this repository.

The variables themselves — `SESSION`, `SESSIONDIR`, `RESULTSDIR`, `USER`,
`HOME`, `PWD` and the rest — are described once, for all tools, in
[Environment variables](../07-toolpaths/01-toolenvvars.md). Read that first.

## Reading them from a cell

From a Python kernel, the environment is a dictionary:

```python
import os

session = os.environ['SESSION']
sessiondir = os.environ['SESSIONDIR']
```

Use `os.environ.get('SESSION')` where the variable may be absent, so that the
notebook still runs when you are developing it outside a tool session.

A shell escape works too, for a quick look:

```python
!echo $SESSION
```

```python
!printenv
```

`%env` lists the environment as IPython sees it. Note that `!` runs each
command in a fresh shell, so `!cd somewhere` does not move the kernel; use
`os.chdir` or the `%cd` magic for that.

## Where to write

Three destinations, and the difference matters more for a notebook than for a
tool with its own file dialogue, because a notebook makes it so easy to write
into the current directory:

| Variable | Use it for |
|---|---|
| `SESSIONDIR` | Scratch files for this run. Goes away with the session. |
| `RESULTSDIR` | Output the user should still have tomorrow. |
| `HOME` | Files the tool keeps between runs, under `$HOME/data/toolname`. |

Build paths from the variables rather than assuming the working directory:

```python
import os

out = os.path.join(os.environ['RESULTSDIR'], 'run.csv')
```

Everything a notebook writes counts against the user's disk quota. Clean up
scratch files when the run finishes.

## What does not persist

Changes a user makes to a published notebook are not saved back to the tool.
Each launch starts from the notebook as it was deployed, so a notebook cannot
use its own cells to remember anything between sessions. Write to
`RESULTSDIR`, or under `HOME`, or nothing survives.

## The session number

`SESSION` is the session's own number, and it also appears in the URL of a
running Jupyter tool, under the hub's session proxy. That is a useful thing to
quote in a support ticket, since it lets an administrator find the session's
logs.
