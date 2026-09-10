<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/testing-jupyter
source-id: 3560
modified: 2022-11-22
imported: 2026-09-09
-->
# Testing a Jupyter tool

Before you tell the pipeline your code is ready, run the invoke script by hand
and open the notebook it starts. This catches the mistakes that are otherwise
found by the hub's staff during installation: a missing kernel, a wrong path
in the invoke script, a notebook that needs a network address the hub blocks.

> **Important:** The test below happens entirely on the tool execution
> platform — the workspace tool, `invoke_app`, `start_jupyter` and the session
> proxy. That platform is separate software and is **not in this repository**,
> so the procedure could not be checked here; it is the written record carried
> over from the platform documentation. The pipeline states it refers to are
> CMS-side and were checked: see
> [Tools](../../../managers/03-maintenance/02-tools.md) in the hub managers
> book.

The example assumes your tool's short name is *toolname* and that you have
checked its repository out into `~/apps/toolname`.

## 1. Have a tool to test

Register the tool first, so that a repository exists and the invoke script is
in it. Registration is a CMS form, and it is where you choose the **Web
application (Jupyter, Rstudio, ...)** publishing option that makes this a
Jupyter tool rather than a Rappture or Linux-GUI one. The form and the states
that follow it are described in
[Tools](../../../managers/03-maintenance/02-tools.md#registering-a-tool).

Ask your hub's administrators which container image new tools should be built
against. It changes over time, and a tool built against a retired image is
work you will have to redo.

## 2. Run the invoke script

Start the hub's workspace tool, or open a terminal in the Jupyter tool, and
run the script from the tool's `middleware` directory:

```bash
cd ~/apps/toolname/middleware
./invoke
```

Read the output. Lines beginning `I` are informational and lines beginning `W`
are warnings; neither means something is wrong. Errors do, and the notebook
will not appear until they are fixed. Fix, commit, and run it again.

A successful run says:

```text
The Jupyter notebook is running
```

and prints a long URL under the hub's session proxy, of the form
`https://proxy.yourhub.org/weber/...`.

> **Note:** That URL is what the CMS hands the browser when a user launches
> a published Jupyter tool. The middleware returns it, and the session
> controller redirects to it rather than rendering a VNC session in the page,
> after setting the session's authentication cookie. That much is CMS-side and
> was checked in
> [`sessions.php`](../../../../core/components/com_tools/site/controllers/sessions.php);
> [Jupyter notebooks](../../administrators/jupyter-notebooks.md) in the
> administrators section describes it.

## 3. Open the notebook

The URL only works from inside your session on the hub. That is deliberate: it
keeps an unreleased tool off the public network.

1. Copy the URL from the invoke output.
2. Start a browser inside the workspace session, from the session's own
   application menu.
3. Paste the URL into it — inside a workspace, the middle mouse button pastes
   the selection — and load it.

What appears is your notebook running as a tool, in the
[deployment style](01-jupyter-deployment-styles.md) your invoke script sets.
Step through it as a user would.

## 4. Watch for blocked addresses

Outbound network connections from a tool session are blocked by default. A
notebook that fetches data from an external service will hang and time out
here, and will do the same after publication.

Collect every address your notebook needs and put them in one support ticket:
name the tool, list the addresses, and say what each is for. The hub's
administrators decide whether to allow them. Doing this during testing is much
faster than discovering it after the tool is installed.

## 5. Stop

Close the browser inside the workspace, then press Ctrl-C in the terminal
running the invoke script. The prompt returns when the notebook has stopped.

When the tool runs the way you want it to, commit and move the tool to
**Uploaded** so the hub's staff can install it.
