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
| `importfile` | Desktop into the session | [Import file](#import-file) |
| `exportfile` | Session out to the desktop | [Export file](#export-file) |

Both work by opening a page in the member's browser, so the browser must allow
pop-ups from the hub for either to complete.

## Where filexfer comes from

filexfer is a helper program started alongside a tool, not something the tool
links against. A tool asks for it in its invoke script, with the `-c` option
that runs a command in the background before the tool starts:

```sh
/usr/bin/invoke_app "$@" -t toolname -C toolname -c filexfer
```

See [Launching tools with invoke scripts](03-invoke.md) for the rest of the
invoke_app options, and [filexfer](06-accesshomedir.md#filexfer) for the
graphical front end members can run themselves inside a workspace.

A tool that never needs a file from the member does not need filexfer, and
leaving it out of the invoke script is the normal thing to do.

Tools that carry their own transfer controls do not need these commands
either. Rappture tools have upload and download built into the Rappture user
interface, and a Jupyter notebook tool has Jupyter's own file browser.

> **Note:** Rappture is deprecated. Its site now redirects to a nanoHUB page
> that is not readable without an account. Hubs still run Rappture tools, but
> a Jupyter notebook is the current path for a new tool on most hubs — see
> [Jupyter Notebooks](10-jupyter-notebooks/README.md).

## What the CMS contributes

The transfer pages that pop up are served by the hub, and their styling is
template-overridable. `com_tools` answers `/tools/assets/css/<file>.css` by
looking for the named stylesheet in three places, in order:

1. the active site template's `css` directory,
2. the component's [`site/assets/css`](../../../core/components/com_tools/site/assets/css) directory,
3. the component's `site/css` directory.

The first one that exists is returned as `text/css`; if none exists the
request gets a 404. The component ships
[`upload.css`](../../../core/components/com_tools/site/assets/css/upload.css)
and
[`download.css`](../../../core/components/com_tools/site/assets/css/download.css)
containing nothing but their licence header, so they are hooks: put a file of
the same name in the template and the transfer pages pick it up. The older
`/tools/site_css.css` route works the same way and has no shipped file behind
it at all.

Nothing else about file transfer is decided in the CMS.
## Import file

`importfile` moves one or more files from the member's desktop into a running
tool session.

> **Important:** `importfile` is part of the filexfer package on the tool
> execution platform. That platform is separate software and is **not in this
> repository**, so nothing on this page could be verified against code here.
> It is the record carried over from the 2.4 documentation, with the help text
> quoted as it stood. Check the command's own `--help` output on your hub
> before relying on an option.

### What it does

Run `importfile` inside a tool session and it opens a page in the member's
browser asking them to choose files from their computer. They pick the files
and submit the form; the files arrive in the session under the names given on
the command line. The command prints the names of the files it actually
received.

The browser must allow pop-ups from the hub, or the page never appears.

When a filename is given for the imported file, the same page also accepts
text pasted from the clipboard. That is the usual way to get text into a
workspace tool, since a terminal or an application inside the session does not
share the desktop clipboard.

### Using it from a tool

Do not call `importfile` on the tool's main thread. It waits for the upload
and it waits indefinitely, so a tool that blocks on it stops responding to the
member who is meant to be answering the prompt.

Run it as a background process instead — typically through a pipe — and have
the tool watch that pipe for the result. Handle the case where the reply never
comes.

A Rappture tool does not need this command; Rappture's own user interface has
upload built in. Neither does a Jupyter notebook tool, which has Jupyter's
file browser.

### Help text

```text
USAGE: /usr/bin/importfile [-f|--for text] [-l|--label text] file file ...

  options:
    -h or --help
      Prints this help message.

    -f or --for <text>
      Short explanation of what the data will be used for; for
      example, "for CNTBands 2.0".  If given, this text is inserted
      into the upload form to help explain what it will be used for.

    -l or --label <text>
      Prompt for subsequent file arguments using this label string.
      The default label just uses the file name.

    -m or --mode acsii|binary|auto
      In "binary" mode, files are transferred exactly as-is.  In
      "ascii" mode, control-M characters are removed, which helps
      when loading Windows files into the Linux environment.  The
      default is "auto", which removes control-M from text files
      but leaves binary files intact.

    -p or --provenance
      Print more verbose results showing the provenance information
      for all files uploaded.  Instead of a series of space-separated
      file names, this produces one line for each file showing the
      final file name and where it came from, which is either the
      file name on the user's desktop or @CLIPBOARD meaning that the
      user pasted information into the text entry area.  For example:
         foo.tgz <= gui15.tar.gz
         bar.txt <= @CLIPBOARD

    --
      Remaining arguments are treated as file names, even if they
      start with a -.

    file
      Uploaded file will be saved in this file name within your
      tool session. If file is @@ then the file is given the same
      name it had before it was uploaded. If no file arguments
      are included, the default is "@@", meaning upload a single
      file and use the name it had on the desktop.

You can use this command to transfer one or more files from your
desktop to your tool session via a web browser.  This command causes
a web page to pop up prompting you for various files on your desktop.
Choose one or more files and submit the form.  The files will be
uploaded to your tool session and saved in the file names specified
on the command line.

This command returns a list of names for files actually uploaded.
```

> **Note:** The usage line is quoted as it stands, misspelling included. The
> descriptions below it name the three modes as `ascii`, `binary` and `auto`.

### See also

- [Export file](#export-file), for the other direction.
- [Getting files in and out of a tool session](README.md), for how filexfer is
  started and what the CMS serves.
## Export file

`exportfile` sends one or more files from a running tool session out to the
member's desktop.

> **Important:** `exportfile` is part of the filexfer package on the tool
> execution platform. That platform is separate software and is **not in this
> repository**, so nothing on this page could be verified against code here.
> It is the record carried over from the 2.4 documentation, with the help text
> quoted as it stood. Check the command's own `--help` output on your hub
> before relying on an option.

### What it does

Run `exportfile` with one or more file names and the hub opens a browser page
for each file, from which the member downloads it. The browser must allow
pop-ups from the hub, or the pages never appear.

The file stays available for a timeout, one day by default, after which the
hub forgets it. `--delete` also removes the file itself, at the timeout or
when the tool session shuts down, which is what you want for a temporary file
the tool generated only to hand over.

Two options change the page the member sees. `--message` puts a fragment of
HTML above the download — a citation request, for instance. `--format html`
makes the server rewrite links inside an HTML file, so that a page exported
with its images and companion pages still works.

### Using it from a tool

`exportfile` can be run in the background, through a pipe off the tool's main
process, so the member keeps using the tool while the download proceeds. This
is optional: unlike [`importfile`](#import-file), which blocks until the
member answers, `exportfile` hands the file over and does not wait for the
member to save it.

A Rappture tool does not need this command; Rappture's own user interface has
download built in. Neither does a Jupyter notebook tool, which has Jupyter's
file browser.

### Help text

```text
USAGE: /usr/bin/exportfile [-t|--timeout secs] [-d|--delete] [-m|--message file] [-f|--format raw|html] file file...

  options:
    -h or --help
      Prints this help message.

    -t or --timeout <seconds>
      Forget about the file after this timeout.  Default is 86,400
      seconds (1 day).

    -d or --delete
      Delete the file after the timeout or when the tool is shut
      down.  Should be used only with temporary files.

     -m or --message
       File containing a fragment of HTML text that will be displayed
       above the download.  It might say "Here is your data," or
       "If you use this data, please cite this source."

     -f or --format <type>
       Choices are "raw" and "html".  Default is "raw".  The "html"
       format causes the server to rewrite links embedded within
       the HTML, so that images can be displayed and links can be
       traversed properly.

    --
      Remaining arguments are treated as file names, even if they
      start with a -.

You can use this command to transfer one or more files from your
tool session to your desktop via a web browser.  A separate web
browser page is opened for each file.  You must have popups enabled
for this to work properly.
```

### See also

- [Import file](#import-file), for the other direction.
- [Getting files in and out of a tool session](README.md), for how filexfer is
  started and what the CMS serves.
