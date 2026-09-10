<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/fileinout
source-id: 3553
modified: 2015-02-18
imported: 2026-09-09
-->
# Getting files in and out of a tool session

A tool session runs on a hub execution host, not on the member's computer. Two
commands move files across that gap: `importfile` uploads a file from the
desktop into the session, and `exportfile` sends a file from the session back
to the desktop. Both are part of the filexfer package.

> **Important:** `importfile`, `exportfile` and filexfer belong to the tool
> execution platform. That platform is separate software and is **not in this
> repository**, so the commands, their options and their behaviour could not
> be verified here; they are the written record carried over from the 2.4
> documentation. The one part of this mechanism that is in the CMS — the
> stylesheets the transfer pages load — is described below and was checked
> against `com_tools`.

## The two commands

| Command | Direction | Page |
|---|---|---|
| `importfile` | Desktop into the session | [Import file](01-importfile.md) |
| `exportfile` | Session out to the desktop | [Export file](02-exportfile.md) |

Both work by opening a page in the member's browser, so the browser must allow
pop-ups from the hub for either to complete.

## Where filexfer comes from

filexfer is a helper program started alongside a tool, not something the tool
links against. A tool asks for it in its invoke script, with the `-c` option
that runs a command in the background before the tool starts:

```sh
/usr/bin/invoke_app "$@" -t toolname -C toolname -c filexfer
```

See [Launching tools with invoke scripts](../03-invoke.md) for the rest of the
invoke_app options, and [filexfer](../06-accesshomedir/03-filexfer.md) for the
graphical front end members can run themselves inside a workspace.

A tool that never needs a file from the member does not need filexfer, and
leaving it out of the invoke script is the normal thing to do.

Tools that carry their own transfer controls do not need these commands
either. Rappture tools have upload and download built into the Rappture user
interface, and a Jupyter notebook tool has Jupyter's own file browser.

> **Note:** Rappture is deprecated. Its site now redirects to a nanoHUB page
> that is not readable without an account. Hubs still run Rappture tools, but
> a Jupyter notebook is the current path for a new tool on most hubs — see
> [Jupyter Notebooks](../10-jupyter-notebooks/README.md).

## What the CMS contributes

The transfer pages that pop up are served by the hub, and their styling is
template-overridable. `com_tools` answers `/tools/assets/css/<file>.css` by
looking for the named stylesheet in three places, in order:

1. the active site template's `css` directory,
2. the component's [`site/assets/css`](../../../../core/components/com_tools/site/assets/css) directory,
3. the component's `site/css` directory.

The first one that exists is returned as `text/css`; if none exists the
request gets a 404. The component ships
[`upload.css`](../../../../core/components/com_tools/site/assets/css/upload.css)
and
[`download.css`](../../../../core/components/com_tools/site/assets/css/download.css)
containing nothing but their licence header, so they are hooks: put a file of
the same name in the template and the transfer pages pick it up. The older
`/tools/site_css.css` route works the same way and has no shipped file behind
it at all.

Nothing else about file transfer is decided in the CMS.
