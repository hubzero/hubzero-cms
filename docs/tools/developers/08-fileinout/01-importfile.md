<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/fileinout/importfile
source-id: 3554
modified: 2015-02-18
imported: 2026-09-09
-->
# Import file

`importfile` moves one or more files from the member's desktop into a running
tool session.

> **Important:** `importfile` is part of the filexfer package on the tool
> execution platform. That platform is separate software and is **not in this
> repository**, so nothing on this page could be verified against code here.
> It is the record carried over from the 2.4 documentation, with the help text
> quoted as it stood. Check the command's own `--help` output on your hub
> before relying on an option.

## What it does

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

## Using it from a tool

Do not call `importfile` on the tool's main thread. It waits for the upload
and it waits indefinitely, so a tool that blocks on it stops responding to the
member who is meant to be answering the prompt.

Run it as a background process instead — typically through a pipe — and have
the tool watch that pipe for the result. Handle the case where the reply never
comes.

A Rappture tool does not need this command; Rappture's own user interface has
upload built in. Neither does a Jupyter notebook tool, which has Jupyter's
file browser.

## Help text

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

## See also

- [Export file](02-exportfile.md), for the other direction.
- [Getting files in and out of a tool session](README.md), for how filexfer is
  started and what the CMS serves.
