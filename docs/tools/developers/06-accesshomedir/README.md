<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/accesshomedir
source-id: 3544
modified: 2014-02-28
imported: 2026-09-09
merged-from: 2.2
-->
# Accessing your home directory

Your hub home directory holds the files your tool sessions read and write. This
section covers the ways to reach it from your own computer, and what the hub
itself offers for managing what is in it.

> **Note:** The home directory lives on the tool platform, which is separate
> software and is not in this repository. Nothing about the SFTP or WebDAV
> servers could be checked here, and those pages are labelled accordingly. The
> parts the CMS provides — the storage meter, the file manager and the SSH key
> form — were checked against the code.

## Where the files are

In a tool session your home directory is `$HOME`, which takes the form
`/home/<hubname>/<username>`. Session and results directories are created
underneath it; see [Tool paths](../07-toolpaths/README.md).

The web server reaches the same files through a separate mount. Both
[`com_tools`](../../../../core/components/com_tools/site/controllers/storage.php)
and the [Members - Account plugin](../../../../core/plugins/members/account/account.php)
build their paths from `/webdav/home/<username>`. `com_tools` will take a
different base from its undeclared `storagepath` parameter; the account plugin
will not.

## Ways in

| Method | Use it for |
|---|---|
| [sFTP](01-sftp.md) | Everyday transfers from a desktop client or the command line. The most common choice. |
| [WebDAV](02-webdav.md) | Mounting your hub storage as a drive or network location. |
| [filexfer](03-filexfer.md) | Moving a file in or out while a tool session is running. |

If you develop with Jupyter, its own upload and download controls reach the same
home directory. See [Jupyter Notebooks](../10-jupyter-notebooks/README.md).

## Managing what is there

The hub's storage manager is part of the CMS. Signed in, open `/tools/storage`.
It shows how much of your quota you have used, lists the files under your home
directory, and lets you delete files and folders. A **Purge** control for old
session data appears only when the hub has set the **Storage Host** option on
`com_tools`; without it the control redirects instead of doing anything.

The **My Sessions** module carries the same figure with a **Manage** link back
to the storage manager. The meter is drawn only when the **Show Storage**
option is on.

> **Note:** Your home directory is separate from group and project file areas.
> Uploading a file here does not put it in a group or a project.
