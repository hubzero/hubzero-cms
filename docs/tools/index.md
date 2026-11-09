<!--
status: draft
-->
# Tools

A **tool** is a simulation or analysis program that runs in a session on
the hub's execution hosts and is displayed in the member's browser, so a
member runs it without installing anything. Tools are developed in a
repository, built and tested through the contribution pipeline, and
published as resources alongside datasets and presentations.

This book covers the three sides of that platform.

## Before you start

You need access to a hub that offers tool sessions. Installing the CMS or
starting the repository's local Docker development hub gives you the tool
pages and settings, but does not provision execution hosts or the services
that launch programs. If you are setting up a new hub, ask its system
administrator which tool platform services are available.

If you want to change the website itself — add a page type, a plugin, or a
new appearance — use [CMS developers](../developers/index.md). This book is
about programs launched through the website.

## Choose your task

- [Tool developers](developers/index.md) — structuring a tool repository,
  invoke scripts, tool paths and environment variables, file transfer,
  the submit command for remote jobs, and Jupyter notebooks as tools.
- [Tool administrators](administrators.md) — installing tool
  dependencies, whitelisting directories, and administering Jupyter.
- [Tool users](users/index.md) — running tools, what the usage figures
  mean, and the simulation usage definitions.

> **Note:** The tool execution platform is separate software from the
> Hubzero CMS in this repository, so most of what these pages describe
> cannot be checked against the code here. Each page says at the top which
> of its material was verified against the CMS and which is platform
> material carried over from the earlier documentation unchecked. A review
> of the CMS screens does not verify the execution environment. Confirm
> platform commands, paths, and available software with the operators of
> the hub where you will run your tool.
