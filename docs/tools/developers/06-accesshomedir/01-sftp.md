<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/accesshomedir/sftp
source-id: 3545
modified: 2014-02-28
imported: 2026-09-09
merged-from: 2.2
-->
# sFTP

sFTP transfers files over SSH. It encrypts commands and data, so passwords and
file contents never cross the network in the clear. It is not FTP: an FTP client
cannot talk to an sFTP server, and an sFTP client cannot talk to an FTP server.

> **Note:** The sFTP server is part of the tool platform, which is separate
> software and is not in this repository, so the connection details below could
> not be checked. The two things the CMS itself does — accepting your SSH public
> key and setting a local password — were checked against the code and are
> marked as verified.

## Before you connect

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
[`core/plugins/members/account/account.php`](../../../../core/plugins/members/account/account.php).*

> **Note:** If `.ssh` or `.ssh/authorized_keys` in your home directory is a
> symbolic link, the hub refuses to read or write the key and says so. Replace
> the link with a real directory or file.

## Graphical clients

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

## Command line

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
