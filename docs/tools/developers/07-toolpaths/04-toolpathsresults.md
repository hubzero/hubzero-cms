<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolpaths/toolpathsresults
source-id: 3552
modified: 2015-02-18
imported: 2026-09-09
-->
# Tool generated files

Where the files a tool writes while it runs should go.

> **Note:** The directories below are created by the tool platform, which is
> separate software and is not in this repository, so their behaviour could not
> be checked against code. The storage manager mentioned at the end is CMS-side
> and was checked.

## Temporary files

Write temporary files to `${SESSIONDIR}` and delete them when you no longer need
them. Files left there are still visible to the member afterwards, who can clear
them from the hub's storage manager.

`/tmp` is the other option. It is not shared with other sessions and it is
cleared when the session ends, so nothing there survives for the member to find
— or to clean up.

## Results

Write output the member will want to keep to `${RESULTSDIR}`. That directory
lives in their home directory, where they can find it after the session ends. A
tool can also read it back and offer the member a list of earlier results.

Both directories count against the member's storage quota. See
[Environment variables](01-toolenvvars.md) for the paths themselves, and
[Accessing your home directory](../06-accesshomedir.md) for the storage
manager and the ways a member reaches these files from their own computer.
