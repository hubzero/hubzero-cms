<!--
status: merged
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/fileinout/exportfile
source-id: 3555
modified: 2015-02-18
imported: 2026-09-09
merged-from: 2.2
-->
# Export File

## Overview

You can use this command to transfer one or more files from your tool session to your desktop via a web browser. A separate web browser page is opened for each file. **You must have popups enabled for this to work properly.**

## Implementation

This script can be implemented as a background process in a non-rappture tool. This allow the user to continue to use the tool while the file downloads to their machine in the background. Typically a pipe is used to run this script off the main process of the tool.

## Help text

```
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

## Material from the 2.2 documentation

> **Note:** The text below comes from the older 2.2 page of the same name, where it differed substantially from the 2.4 page above. Reconcile the two when reviewing.

## Overview

You can use this command to transfer one or more files from your tool session to your desktop via a web browser. A separate web browser page is opened for each file. You must have popups enabled for this to work properly.

## Implementation

This script can be implemented as a background process in a non-rappture tool. This allow the user to continue to use the tool while the file downloads to their machine in the background. Typically a pipe is used to run this script off the main process of the tool. This is optional

## Help text

USAGE: /usr/bin/exportfile [-t|--timeout secs] [-d|--delete] [-m|--message file] [-f|--format raw|html] file file...

options:
-h or --help
Prints this help message.

-t or --timeout &amp;lt;seconds>
Forget about the file after this timeout. Default is 86,400
seconds (1 day).

-d or --delete
Delete the file after the timeout or when the tool is shut
down. Should be used only with temporary files.

-m or --message
File containing a fragment of HTML text that will be displayed
above the download. It might say "Here is your data," or
"If you use this data, please cite this source."

-f or --format &amp;lt;type>
Choices are "raw" and "html". Default is "raw". The "html"
format causes the server to rewrite links embedded within
the HTML, so that images can be displayed and links can be
traversed properly.

--
Remaining arguments are treated as file names, even if they
start with a -.

You can use this command to transfer one or more files from your
tool session to your desktop via a web browser. A separate web
browser page is opened for each file. You must have popups enabled
for this to work properly.
