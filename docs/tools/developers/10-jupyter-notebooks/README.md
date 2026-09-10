<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks
source-id: 3557
modified: 2019-09-06
imported: 2026-09-09
-->
# Jupyter notebooks

A Jupyter notebook can be published as a hub tool. Other members then run the
notebook from the hub, step through its cells and change them, without any of
their changes reaching your copy. This page introduces the section; the
chapters that follow cover the parts of the job in detail.

> **Important:** A Jupyter tool is mostly a matter for the tool execution
> platform — the Anaconda environments, the container image, `invoke_app` and
> `start_jupyter`, and the proxy that serves the notebook to the browser. That
> platform is separate software and is **not in this repository**, so those
> parts could not be verified here. The **What the CMS does** section below
> was checked against `com_tools`; everything else is the record carried over
> from the 2.4 documentation.

Jupyter is the current path for a new tool on most hubs. Rappture, the older
route, is deprecated: `rappture.org` now redirects to a nanoHUB page that
anonymous readers cannot open. Hubs still run Rappture tools, so the Rappture
material elsewhere in this book stays, but it is no longer the default choice.

## What the CMS does

Almost nothing, and knowing exactly what is worth the paragraph.

`com_tools` has an **Enable Jupyter** option, on by default. While it is on,
the registration form's **Publishing Option** offers **Web application
(Jupyter, Rstudio, ...)** next to **Rappture or Linux-GUI based tool** and
**Sim2L**. Choosing it stores `publishType` as `jupyter` on the tool version.
Hubs upgraded from older releases may hold the historical value `weber=`; the
component reads that as `jupyter`, so the two behave alike.

From there the value is passed straight through to the platform's install and
publish scripts as `--publishOption`. The CMS does not treat a Jupyter tool
differently anywhere in the pipeline: same states, same buttons, same
repository, same `middleware/invoke` script.

The difference appears at launch. A standard tool comes back from the
middleware as a VNC session, which the `novnc` plugin renders in the page. A
Jupyter tool comes back with a proxy URL, and the CMS redirects the browser to
it. If the middleware also returns an authentication token, the CMS first sets
a `weber-auth-<hub domain>` cookie — secure, HttpOnly, thirty days.

Everything else — which Anaconda environments exist, which kernels a notebook
can pick, how the notebook is served — is decided on the execution hosts. See
[Jupyter notebooks](../../administrators.md#jupyter-notebooks) in the tool
administrators book for the administrator's side of that.

## Getting a notebook published

A notebook tool goes through the same pipeline as any other tool, and
[Tools](../../../managers/03-maintenance/02-tools.md) in the hub managers book
documents that pipeline state by state, from the CMS code. The short version,
with the Jupyter-specific parts marked:

1. **Register the tool.** On the hub, create a new tool. Give it an alias
   (short, no spaces, and unchangeable afterwards), a title and a one-line
   description; choose the repository host; add yourself to the development
   team; and set **Publishing Option** to **Web application (Jupyter,
   Rstudio, ...)**. Access can be restricted to a hub group here.
2. **Wait for the repository.** An administrator moves the tool to
   **Created**, which is when the source repository and project area exist.
3. **Check out the repository.** Open your hub's Jupyter tool, start a
   terminal from it, and clone or check out the tool repository into your
   notebooks directory. The commands are on the managers page.
4. **Add the notebook.** Put the main notebook at the top of the tool
   directory and any supporting Python files in a subdirectory, which you then
   import as a module. Additional packages come from the kernel the notebook
   selects; ask the hub administrator for a kernel that does not exist yet.
5. **Edit the invoke script.** `middleware/invoke` is created with the tool
   and has to be edited to start the notebook, which is where `start_jupyter`
   and the Anaconda environment are named.
6. **Test it, then commit.** Test the invoke script before installing
   anything.
7. **Install, test, approve, publish.** Use the tool's status page at
   `/tools/<alias>/status`. The link *"My code is committed, working, and
   ready to be installed"* hands the tool to an administrator; after
   installation you test it, complete the tool information page and licence,
   and approve it for publication.

To change a published notebook, edit it, test it, commit it, and go round the
install-test-publish part again. The published version keeps serving members
until the new one replaces it.

> **Note:** Some hubs need a support ticket to register a new tool against the
> current container image, so that it gets current packages and kernels. Ask
> your hub administrator whether yours does. Use the newest Jupyter and
> Anaconda versions your hub deploys.

## In this section

- [Jupyter tool deployment styles](01-jupyter-deployment-styles.md) — whether
  members see the notebook's code cells or only its widgets, and how to
  choose.
- [Using Python packages from Jupyter notebooks](02-pyton-from-jupyter.md) —
  kernels, and what to do about a package the hub does not have. (The filename
  misspells *python*; the URL is published, so it stays.)
- [Testing Jupyter-based tools](03-testing-jupyter.md) — running the tool from
  a workspace before it goes near the pipeline.
- [Environment variables](04-environment-variables.md) — what a tool session
  can read about itself.
- [Invoke scripts for Jupyter notebooks](05-invoke-jupyter.md) — `invoke_app`,
  `start_jupyter` and their options.

For invoke scripts in general, including the options shared with every other
kind of tool, see
[Launching tools with invoke scripts](../03-invoke.md). For running a notebook
job on the grid, see
[Jupyter integration with submit](../05-grid/04-jupyter_submit.md).
