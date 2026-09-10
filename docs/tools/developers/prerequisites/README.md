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

- [Environment](environment.md) — the container a tool session runs in and
  the environment variables your tool can rely on.

Two pages outside this section are worth reading first:

- [Tools](../../../users/22-tools.md) in the users book says what a tool
  session looks like to the person running it.
- [Tools](../../../managers/03-maintenance/02-tools.md) in the hub managers
  book documents the contribution pipeline, including every field on the
  registration form. [The contribution process](../process.md) summarises the
  same pipeline from the developer's side.

You also need an account on the hub you are publishing to, and that account
has to be allowed to register a tool. Hubs are normally configured to let any
member register one.
