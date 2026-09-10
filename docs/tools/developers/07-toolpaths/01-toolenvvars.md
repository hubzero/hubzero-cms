<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolpaths/toolenvvars
source-id: 3549
modified: 2015-02-18
imported: 2026-09-09
-->
# Environment variables

A tool session sets a number of environment variables. The useful ones are
below; `env` in a session terminal lists them all.

> **Note:** These variables are set by the tool platform, which is separate
> software and is not in this repository, so the values below could not be
> checked against code.

A tool runs as the member who launched it, with that member's permissions. That
is why it can write to their home directory.

| Variable | Value | What it is for |
|---|---|---|
| `SESSION` | The session number | Identifies the running session. It also appears in the browser URL of the session. |
| `USER` | The member's username | The account the tool runs as. |
| `SESSIONDIR` | `/home/<hubname>/<username>/data/sessions/<session>` | A directory of its own for each session. The working directory a tool starts in, and the right place for temporary files. |
| `RESULTSDIR` | `/home/<hubname>/<username>/data/results/<session>` | Where to leave results the member will want after the session ends. |
| `PWD` | The present working directory | Standard shell variable. |
| `HOME` | `/home/<hubname>/<username>` | The member's home directory. |

Both `SESSIONDIR` and `RESULTSDIR` count against the member's storage quota.

If a tool saves work outside the session, put it under a directory of its own —
`$HOME/data/<toolname>` — rather than scattering files across the home
directory. Unlike `SESSIONDIR` and `RESULTSDIR`, that directory is not created
for you.

Rappture writes its results to `RESULTSDIR`. Rappture is deprecated; on most
hubs a Jupyter notebook is the current path. See
[Jupyter Notebooks](../10-jupyter-notebooks/README.md), whose
[environment variables page](../10-jupyter-notebooks/04-environment-variables.md)
covers the same variables from a notebook.
