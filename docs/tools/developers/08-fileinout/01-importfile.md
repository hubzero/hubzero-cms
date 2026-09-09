<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/fileinout/importfile
source-id: 3554
modified: 2015-02-18
imported: 2026-09-09
-->
# Import File

## Overview

"importfile" is the command line tool that, when run, opens a pop-up window prompting the user to browse and select file(s) from their computer to be uploaded. If a filename is provided for the imported file the pop-up window can alternatively be used to paste text from the user clipboard. This is most useful in workspace tools which do not allow text to be directly pasted to terminals or other applications.

You can use this command to transfer one or more files from your desktop to your tool session via a web browser. This command causes a web page to pop up prompting you for various files on your desktop. Choose one or more files and submit the form. The selected files will be uploaded to your tool session and saved with the file names specified on the command line. **You must have popups enabled for this to work properly.**

## Implementation

This script should be implemented as a background process in a non-rappture tool. Typically a pipe is used to run this script off the main process of the tool. The piped process should be monitored by the tool code for a response upon completion of the user's file upload. The script will continue to wait for a file to be uploaded indefinitely. Please code appropriately for this.

## Help text

```
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
