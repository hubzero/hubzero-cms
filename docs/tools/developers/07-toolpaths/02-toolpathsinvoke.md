<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolpaths/toolpathsinvoke
source-id: 3550
modified: 2015-02-19
imported: 2026-09-09
-->
# Passing path variables with the invoke script

A tool that ships example inputs or static data has to find them at run time.
The invoke script tells it where its own repository is, using the `@tool`
substitution.

> **Note:** `invoke_app` and the `@tool` substitution belong to the tool
> platform, which is separate software and is not in this repository, so they
> could not be checked against code. The launch parameters described at the
> bottom of this page are CMS-side and were checked.

## @tool

`invoke_app` replaces `@tool` with the tool's directory. Pass it to your program
in either of two ways.

As an argument:

```text
-A @tool
```

As an environment variable:

```text
-e TOOL_REPO_PATH=@tool
```

`TOOL_REPO_PATH` is a name of your own choosing; nothing on the platform
requires it.

For the rest of the options, see
[Launching tools with invoke scripts](../03-invoke.md).

## Paths that come from the launch URL

A tool can also be launched with file and directory names in the URL, through
the `params` argument on `/tools/<alias>/invoke`. Those values are checked by
`com_tools` before a session starts: each one has to be an absolute path under a
directory the hub has whitelisted, and one bad value rejects the whole launch.
See [Directory parameter whitelist](../../administrators/whitelistdirectories.md)
for what is accepted and what the member sees when it is not.
