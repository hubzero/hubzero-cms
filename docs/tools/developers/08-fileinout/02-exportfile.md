<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/fileinout/exportfile
source-id: 3555
modified: 2015-02-18
imported: 2026-09-09
merged-from: 2.2
-->
# Export file

`exportfile` sends one or more files from a running tool session out to the
member's desktop.

> **Important:** `exportfile` is part of the filexfer package on the tool
> execution platform. That platform is separate software and is **not in this
> repository**, so nothing on this page could be verified against code here.
> It is the record carried over from the 2.4 documentation, with the help text
> quoted as it stood. Check the command's own `--help` output on your hub
> before relying on an option.

## What it does

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

## Using it from a tool

`exportfile` can be run in the background, through a pipe off the tool's main
process, so the member keeps using the tool while the download proceeds. This
is optional: unlike [`importfile`](01-importfile.md), which blocks until the
member answers, `exportfile` hands the file over and does not wait for the
member to save it.

A Rappture tool does not need this command; Rappture's own user interface has
download built in. Neither does a Jupyter notebook tool, which has Jupyter's
file browser.

## Help text

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

## See also

- [Import file](01-importfile.md), for the other direction.
- [Getting files in and out of a tool session](README.md), for how filexfer is
  started and what the CMS serves.
