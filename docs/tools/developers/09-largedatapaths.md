<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/largedatapaths
source-id: 3556
modified: 2016-01-25
imported: 2026-09-09
-->
# Large data paths

Where a large dataset lives on a hub, so that a tool session can read it
without every member carrying a copy in their home directory.

> **Important:** The directories below are created on the hub's shared
> filesystem and mounted into tool containers by the tool execution platform.
> That platform is separate software and is **not in this repository**, so the
> paths, permissions and mounts could not be verified here; they are the
> convention recorded in the 2.4 documentation and a hub may differ. The one
> CMS setting that touches these paths is described at the end of the page and
> was checked against `com_tools`.

## Asking for space

Large data space is granted on request, not by default. Agreeing to it is a
decision for the people who run the hub: how much disk is left, how big the
dataset is, and whether it belongs on the hub at all. Settle that first, then
open a support ticket asking for the directory to be created.

## A directory for a tool

```text
/data/tools/<toolname>
```

Mode 775, writable by the tool's development group, readable by everyone else.
Data kept here is meant to be read by that tool while it runs.

The development group is `app-<toolname>` — `com_tools` creates it when the
tool registration is saved, naming it from the **Dev group prefix** option
(default `app-`) and the tool alias in lower case, and sets its membership
from the development team named on the form. That much is CMS-side and
verified. Whether the group grants write access on the shared
filesystem is the platform's business.

## A directory for a group

```text
/data/groups/<groupname>
```

Mode 775, writable by the members of that hub group. Data kept here is
maintained by the group and can be read by more than one tool.

## Reaching it from a session

Both `/data/tools` and `/data/groups` are mounted in tool containers, so a
tool reads them as ordinary paths. Nothing is copied into the session and
nothing counts against the member's home directory quota.

For the other directories a tool session sees, see
[Tool paths](07-toolpaths/README.md).

## Launching a tool at a large data path

A tool session can be started with file and directory names in the launch
URL's `params` argument. `com_tools` refuses any path that does not begin with
a directory on the **Directory Parameter Whitelist**, and the shipped default
lists only `/home`. So pointing a launch link at something under `/data`
requires an administrator to add that prefix to the whitelist first. See
[Directory parameter whitelist](../administrators/whitelistdirectories.md)
for the check and how to change it.
