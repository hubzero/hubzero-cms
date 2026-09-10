<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/jupyter-deployment-styles
source-id: 3558
modified: 2022-11-22
imported: 2026-09-09
-->
# Deployment styles

A published notebook can show its code to the person running it, hide the
code behind the widgets, or hide it for good. You choose the style once, in
the tool's invoke script, and it applies to everyone who launches the tool.

> **Important:** Display styles are decided by `start_jupyter` on the tool
> execution platform, which is separate software and is **not in this
> repository**. The flags and behaviour below are the written record carried
> over from the platform documentation and could not be checked here. The one
> CMS-side fact — that a tool has to be registered with the **Web application
> (Jupyter, Rstudio, ...)** publishing option before any of this applies — was
> checked against `com_tools`; see
> [Jupyter notebooks](../../administrators.md#jupyter-notebooks) in the
> administrators section.

## The three styles

| Style | What the user sees | `start_jupyter` flags |
|---|---|---|
| Notebook | Every code cell, and the full notebook interface. The default. | none |
| App | Widgets, markdown and output only. An **Edit App** button reveals the code. | `-A` |
| Tool | Widgets, markdown and output only. No way to reveal the code. | `-A -t` |

Notebook style suits a shared analysis, where the point is the code and the
reader is expected to change it. App style suits teaching and demonstrations:
the tool works out of the box, and the curious can still look inside. Tool
style suits a finished application whose code would only distract, or whose
author does not want it read in the browser.

> **Note:** Tool style hides the code from the running interface. It is not a
> licensing control. Whether people can obtain the source is set by the
> licence you choose in the pipeline — see
> [Tools](../../../users/22-tools.md#contributing-a-tool) in the users book.

Whichever style you pick, changes a user makes while running your published
tool are not saved back to it. Each launch starts from the notebook as
deployed.

## Choosing a style

App and Tool style both rely on the Appmode extension in the hub's Jupyter
installation. Where the hub has it, an **Appmode** button appears in the
notebook toolbar, and pressing it shows the notebook as App style will show
it; **Edit App** switches back. Use that button to check the layout before
you deploy, since a notebook that reads well with its code visible often
needs rearranging without it.

If the button is not there, ask the hub's administrators which Jupyter
version and extensions they deploy before committing to App or Tool style.

## Setting the style

The flags in the table go on the `start_jupyter` call inside the invoke
script, not on `invoke_app`. Both commands happen to use `-t`, and they mean
different things:

- `invoke_app -t` names the tool.
- `start_jupyter -t` hides the code cells for good.

[Invoke scripts for Jupyter notebooks](05-invoke-jupyter.md) gives the full
script for each of the three styles, and the rest of the arguments.
