<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolpaths/toolpathexamples
source-id: 3551
modified: 2015-02-18
imported: 2026-09-09
-->
# Example files

Where the input files you ship with a tool should live.

> **Note:** These are conventions of the tool platform, which is separate
> software and is not in this repository, and could not be checked against code.
> The size threshold in particular is a guideline, not something enforced.

Small files belong in the tool repository, in its `data` or `examples`
directory. The invoke script points the tool at them with `@tool`; see
[Passing path variables with the invoke script](02-toolpathsinvoke.md).

Large files — roughly 100MB and up — do not belong in the repository. They go in
a directory under `/data`, which is normally organised by tool or by group and
is mounted into the tool containers. Either location suits a static dataset that
does not change with the tool.

> **Note:** A `/data` directory is set up by request. Ask your hub
> administrator, and see [Large data paths](../09-largedatapaths.md).
