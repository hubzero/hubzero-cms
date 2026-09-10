<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/grid
source-id: 3539
modified: 2012-09-20
imported: 2026-09-09
-->
# Accessing outside computing resources

A tool runs in a tool session on the hub's execution hosts. The session
carries the interface the user works in. Light calculations run in the
session itself; heavier ones are sent to a cluster or a national resource
with the `submit` command, which keeps the session responsive and returns
the results when the job finishes.

> **Note:** `submit` is part of the tool execution platform, which is
> separate software and is **not in this repository**. Nothing on these
> pages could be checked against the code here, and the CMS holds no trace
> of the command — the only mention left in `com_tools` is
> [a migration that dropped an old `submit` sessions table](../../../../core/components/com_tools/migrations/Migration20180702134743ComToolsRemoveSubmitSessions.php).
> The material is kept as the written record, corrected where it
> contradicted itself, and `submit --help` on your own hub is the
> authority on what your client accepts.

## The pages here

Most tools reach `submit` from a Jupyter notebook now, so
[Submitting from a Jupyter notebook](04-jupyter_submit.md) is the page to
read if you are writing a new tool; the submit command reference below
explains what the options it sets actually do.

- [Submit command](01-submitcmd.md) — what `submit` does, its options, and
  the parameter sweep syntax. Start here whichever way you call it.
- [Submitting from a Jupyter notebook](04-jupyter_submit.md) — calling
  `submit` from a cell, and the `SubmitCommand` class from the
  `hubzero.submit` library.
- [Pegasus workflow submission](02-pegasuswf.md) — running a workflow you
  built yourself, rather than a sweep `submit` generates for you.
- [Submitting from a Rappture tool](03-rappture_submit.md) — kept for hubs
  that still run Rappture tools. Rappture is deprecated; do not start a new
  tool with it.

Related reading: [Jupyter notebooks as tools](../10-jupyter-notebooks/README.md)
for publishing a notebook in the first place, and
[registering a tool](../../../managers/03-maintenance/02-tools.md) for the
publishing option that marks a tool as a web application.
