<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/index/fileaccess
source-id: 3424
imported: 2026-09-09
source-state: unpublished
-->
# Accessing Files

## Accessing via SSH

The following tutorial should help you in using SSH to connect to and from your HUBzero server(s). You should be relatively comfortable with using a terminal (also referred to as a "command-line tool") to navigate directories and manipulate files.

> **Warning:** Most accounts do **not** have SSH/sFTP access initially. Your system administrator must grant your account access before you will be able to connect.

From a terminal type `ssh <user>@<host>`. You will then be prompted for a password. Both the username and password will typically be the same as the account you registered on `<host>`.

```
yourmachine:~ you$ ssh username@host
yourmachine:~ you$ username@host password:

host ~
```

### Windows Clients

- [PuTTY](http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html) (a Telnet and SSH client)

### Mac OSX

All versions of Mac OSX come with Terminal.app which may be found in the `/Utilities` directory of your `/Applications` directory.

## Accessing via sFTP

sFTP, or secure FTP, is a program that uses SSH to transfer files. Unlike standard FTP, it encrypts both commands and data, preventing passwords and sensitive information from being transmitted in the clear over the network. It is functionally similar to FTP, but because it uses a different protocol, you can't use a standard FTP client to talk to an sFTP server, nor can you connect to an FTP server with a client that supports only sFTP.

The following tutorial should help you in using sFTP to connect to and from your HUBzero server(s).

> **Warning:** Most accounts do **not** have SSH/sFTP access initially. Your system administrator must grant your account access before you will be able to connect.

### Graphical Clients

Using graphical SFTP clients simplifies file transfers by allowing you to transmit files simply by dragging and dropping icons between windows. When you open the program, you will have to enter the name of the host (e.g., yourhub.org) and your HUB username and password.

#### Windows Clients

- [WinSCP](http://winscp.net/)
- [BitKinex](http://www.bitkinex.com/sftpclient/)
- [FileZilla](http://filezilla-project.org/)
- [PuTTY](http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html)

#### Mac OSX Clients

- [Transmit](http://www.panic.com/transmit/)
- [Fetch](http://fetchsoftworks.com/)
- [Cyberduck](http://cyberduck.ch/)
- [Flow](http://extendmac.com/flow/)
- [Fugu](http://rsug.itd.umich.edu/software/fugu/)

### Command-line

You can use command line SFTP from your Unix account, or from your Mac OS X or Unix workstation. To start an SFTP session, at the command prompt, enter:

```
yourmachine:~ you$ sftp username@host
yourmachine:~ you$ username@host password:

host ~
```

| Command | Description |
|---|---|
| `cd` | Change the directory on the remote computer |
| `chmod` | Change the permissions of files on the remote computer |
| `chown` | Change the owner of files on the remote computer |
| `dir` (or `ls`) | List the files in the current directory on the remote computer |
| `exit` (or `quit`) | Close the connection to the remote computer and exit SFTP |
| `get` | Copy a file from the remote computer to the local computer |
| `help` (or `?`) | Get help on the use of SFTP commands |
| `lcd` | Change the directory on the local computer |
| `lls` | See a list of the files in the current directory on the local computer |
| `lmkdir` | Create a directory on the local computer |
| `ln` (or `symlink`) | Create a symbolic link for a file on the remote computer |
| `lpwd` | Show the current directory (present working directory) on the local computer |
| `lumask` | Change the local umask value |
| `mkdir` | Create a directory on the remote computer |
| `put` | Copy a file from the local computer to the remote computer |
| `pwd` | Show the current directory (present working directory) on the remote computer |
| `rename` | Rename a file on the remote host |
| `rm` | Delete files from the remote computer |
| `rmdir` | Remove a directory on the remote host (the directory usually has to be empty) |
| `version` | Display the SFTP version |
| `!` | In Unix, exit to the shell prompt, where you can enter commands. Enter `exit` to get back to SFTP. If you follow `!` with a command (e.g., `!pwd`), SFTP will execute the command without dropping you to the Unix prompt. |

## Finding Files

Once connected to a server, by either sFTP or directly with SSH, you will need to find the web root which contains the HUB install. The web root for the production version of a HUB can be found at `/www/yourhub`. Typically, HUBs will also have a development version of a HUB, which can be found at `/www/dev`.

Once in the desired directory, file layout and directory structure follows the conventions detailed in [Structure](../03-foundation/01-structure.md) unless otherwise noted.

> **Note:** See the [overview](../03-foundation/01-structure.md) for details on a typical HUBzero install's directory structure.
