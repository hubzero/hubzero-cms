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
underneath it; see [Tool paths](07-toolpaths/README.md).

The web server reaches the same files through a separate mount. Both
[`com_tools`](../../../core/components/com_tools/site/controllers/storage.php)
and the [Members - Account plugin](../../../core/plugins/members/account/account.php)
build their paths from `/webdav/home/<username>`. `com_tools` will take a
different base from its undeclared `storagepath` parameter; the account plugin
will not.

## Ways in

| Method | Use it for |
|---|---|
| [sFTP](#sftp) | Everyday transfers from a desktop client or the command line. The most common choice. |
| [WebDAV](#webdav) | Mounting your hub storage as a drive or network location. |
| [filexfer](#filexfer) | Moving a file in or out while a tool session is running. |

If you develop with Jupyter, its own upload and download controls reach the same
home directory. See [Jupyter Notebooks](10-jupyter-notebooks/README.md).

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
## sFTP

sFTP transfers files over SSH. It encrypts commands and data, so passwords and
file contents never cross the network in the clear. It is not FTP: an FTP client
cannot talk to an sFTP server, and an sFTP client cannot talk to an FTP server.

> **Note:** The sFTP server is part of the tool platform, which is separate
> software and is not in this repository, so the connection details below could
> not be checked. The two things the CMS itself does — accepting your SSH public
> key and setting a local password — were checked against the code and are
> marked as verified.

### Before you connect

> **Warning:** Accounts do not get SSH and sFTP access automatically. A hub
> administrator has to grant it before any of this works.

Two things on the hub decide whether you can authenticate.

**Your local services password.** If you signed in through an external provider
— an institutional login, a social account — you have no password the SSH server
can check. Open your profile's **Account** tab and use **Request token** to set
a local password for SSH, sFTP and tool development access. *Verified against
`core/plugins/members/account/account.php`.*

**Your SSH public key.** The same **Account** tab shows a **Local services
account** section with your local services username and a **Manage Public SSH
keys** box. Paste your public key there and submit; the hub writes it to
`.ssh/authorized_keys` in your home directory. The section only appears when the
hub has turned on the plugin's **Show local services details** option, so if you
do not see it, ask an administrator. *Verified against
[`core/plugins/members/account/account.php`](../../../core/plugins/members/account/account.php).*

> **Note:** If `.ssh` or `.ssh/authorized_keys` in your home directory is a
> symbolic link, the hub refuses to read or write the key and says so. Replace
> the link with a real directory or file.

### Graphical clients

A graphical client lets you drag files between windows. Give it the host name of
your hub — `yourhub.org` — and your hub username, then your password or your key.

**Linux.** Most file managers speak sFTP directly. In GNOME Files, KDE Dolphin
or Nemo, open the location bar (Ctrl+L in GNOME) and enter
`sftp://username@yourhub.org`. Your files then behave like local ones.

**Windows.** [WinSCP](https://winscp.net/),
[FileZilla](https://filezilla-project.org/), and
[PuTTY](https://www.chiark.greenend.org.uk/~sgtatham/putty/), whose `psftp`
command is its sFTP client.

**macOS.** [Transmit](https://panic.com/transmit/),
[Fetch](https://fetchsoftworks.com/),
[Cyberduck](https://cyberduck.io/), and
[ForkLift](https://binarynights.com/).

### Command line

`sftp` ships with OpenSSH and is available on Linux and in the macOS terminal.

```bash
sftp username@yourhub.org
```

Once connected you get an `sftp>` prompt.

| Command | Description |
|---|---|
| `cd` | Change the directory on the remote computer |
| `chmod` | Change the permissions of files on the remote computer |
| `chown` | Change the owner of files on the remote computer |
| `dir` (or `ls`) | List the files in the current directory on the remote computer |
| `exit` (or `quit`) | Close the connection and leave sFTP |
| `get` | Copy a file from the remote computer to the local computer |
| `help` (or `?`) | Get help on the use of sFTP commands |
| `lcd` | Change the directory on the local computer |
| `lls` | List the files in the current directory on the local computer |
| `lmkdir` | Create a directory on the local computer |
| `ln` (or `symlink`) | Create a symbolic link for a file on the remote computer |
| `lpwd` | Show the current directory on the local computer |
| `lumask` | Change the local umask value |
| `mkdir` | Create a directory on the remote computer |
| `put` | Copy a file from the local computer to the remote computer |
| `pwd` | Show the current directory on the remote computer |
| `rename` | Rename a file on the remote host |
| `rm` | Delete files from the remote computer |
| `rmdir` | Remove a directory on the remote host, which usually has to be empty |
| `version` | Display the sFTP protocol version |
| `!` | Drop to a local shell. Follow it with a command, as in `!pwd`, to run just that command. |
## WebDAV

WebDAV is the Distributed Authoring and Versioning extension to HTTP. It lets a
client browse a remote filesystem, usually in a graphical browser that makes
your hub files look like files on your desktop. Hubs offer WebDAV over HTTPS
only; plain HTTP is not supported.

> **Note:** The WebDAV server is part of the tool platform, which is separate
> software and is not in this repository, so none of the client procedures below
> could be checked. What the CMS knows is only the mount the web server writes
> through, `/webdav/home/<username>`, which is not the address you connect to.

### The address

On a standard installation the endpoint is:

```text
https://yourhub.org/webdav/
```

Some hubs put it on a host of its own, `https://webdav.yourhub.org/`. If neither
works, ask your hub administrator which one it uses. You authenticate with your
hub login and password.

### Linux

Most desktop file managers speak WebDAV. In the location bar of GNOME Files, KDE
Dolphin or Nemo, enter:

```text
davs://username@yourhub.org/webdav
```

Some file managers offer a **Connect to Server** entry instead. Choose **Secure
WebDAV (HTTPS)** and give `/webdav` as the folder.

![The Connect to Server menu entry in a Linux file manager](../media/webdav-connect-menu-linux.png)![The Connect to Server dialog, with Secure WebDAV selected](../media/webdav-connect-dialog-linux.png)

To mount your hub storage as a filesystem rather than browse it, use
[davfs2](https://savannah.nongnu.org/projects/davfs2). For a text-mode client,
`cadaver` behaves much like FTP; run it against the address above and it prompts
for your login.

### macOS

1. In the Finder, choose **Go → Connect to Server**.
2. Enter the address for your hub and select **Connect**.
3. Enter your hub login and password when prompted.

The hub then appears in the Finder and you can drag files and folders to and
from it.

### Windows

Windows connects through the **WebClient** service, which is not always running.
Turn it on first:

1. Open **Services**.
2. Find **WebClient**, set its startup type to **Automatic**, and select
   **Apply**.
3. If it is not running, select **Start**.

Then map the hub as a network location:

1. Open **File Explorer** and select **This PC**.
2. Select **Map network drive → Connect to a Web site that you can use to store
   your documents and pictures**.
3. Select **Next**, then **Choose another network location**, then **Next**.
4. Enter the address for your hub and select **Next**.
5. Enter your hub credentials.
6. Name the location and finish the wizard.

The new network location appears under **This PC**. Open it and drag files and
folders between it and your computer.

> **Tip:** If dragging does not work, right-click the file or folder and choose
> **Copy**, then right-click the destination and choose **Paste**.
## filexfer

`filexfer` moves a file between your computer and a running tool session,
without going through sFTP or WebDAV first.

> **Note:** `filexfer` belongs to the tool platform, which is separate software
> and is not in this repository. Searching `com_tools` and the rest of the CMS
> finds no trace of it, so the CMS neither provides nor configures it and
> nothing below could be checked here.

### Using it

Start the Workspace tool and type `filexfer` at the terminal prompt. The
interface offers **Upload** and **Download**. Choosing either opens a browser
window for the transfer, so your browser must allow pop-ups from the hub.

![The filexfer window, offering Upload and Download](../media/filexfer-2014-02-28-05-49-22-pm.jpg)

Other tools can include `filexfer` as a secondary application, which gives their
users the same way of exchanging files with a session.

### From inside a tool

A tool does not call `filexfer` itself. It calls the two commands that ship with
it, `importfile` and `exportfile`. See
[Importing and exporting user files](08-fileinout.md).
