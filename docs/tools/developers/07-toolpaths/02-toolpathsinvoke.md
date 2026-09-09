<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolpaths/toolpathsinvoke
source-id: 3550
modified: 2015-02-19
imported: 2026-09-09
-->
# Passing path variables with the Invoke Script

## Overview

Passing variables for use in the runtime tool environment is a common occurrence, in particular "@tool" .

See the [full invoke_app documentation](../03-invoke.md).

## @tool

The variable "@tool" can be passed into your tool via the invoke script. This is important information for you tool to know so that the tool can access example input files and static data files that reside in the respective directories. There are two way to pass the "@tool" location via the invoke script to the tool.

\1) As an argument to the tool

`-A @tool`

\2) As an environment variable

`-e TOOL_REPO_PATH=@tool`
