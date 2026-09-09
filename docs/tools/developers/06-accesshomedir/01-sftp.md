<!--
status: merged
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/accesshomedir/sftp
source-id: 3545
modified: 2014-02-28
imported: 2026-09-09
merged-from: 2.2
-->
# sFTP

## Accessing your home directory via sFTP

sFTP, or secure FTP, is a program that uses SSH to transfer files. Unlike standard FTP, it encrypts both commands and data, preventing passwords and sensitive information from being transmitted in the clear over the network. It is functionally similar to FTP, but because it uses a different protocol, you can't use a standard FTP client to talk to an sFTP server, nor can you connect to an FTP server with a client that supports only sFTP.

The following tutorial should help you in using sFTP to connect to and from your HUBzero server(s).

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

## Material from the 2.2 documentation

> **Note:** The text below comes from the older 2.2 page of the same name, where it differed substantially from the 2.4 page above. Reconcile the two when reviewing.

## SFTP

SFTP, or Secure FTP, is a program that uses SSH to transfer files. Unlike standard FTP, it encrypts both commands and data, preventing passwords and sensitive information from being transmitted in the clear over the network. It is functionally similar to FTP, but because it uses a different protocol, you can't use a standard FTP client to talk to an SFTP server, nor can you connect to an FTP server with a client that supports only SFTP.

The following tutorial should help you in using SFTP to connect to and from your HUBzero server(s).

> **Warning:** Most accounts do **not** have SSH/SFTP access initially. Your system administrator must grant your account access before you will be able to connect.

### Graphical Clients

Using graphical SFTP clients simplifies file transfers by allowing you to transmit files simply by dragging and dropping icons between windows. When you open the program, you will have to enter the name of the host (e.g., yourhub.org) and your HUB username and password. The use of graphical SFTP clients is highly recommended if you have access to it. Done properly they allow you to quickly and easily access your HUB files as if they are local files on your desktop.

#### Linux

- From the file browser, type "sftp://yourhub.org". Use the name of your hub instead of "yourhub.org". If you don't see the location field at the top of the file browser window, hit Ctrl+L to toggle it. Your files will now be available through the file browser like local files.

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
- [Forklift](https://www.binarynights.com/forklift/)Version2 is available for free through the app store.

#### Command-line

You can use command line SFTP from Linux, or from your Mac OS X terminal. To start an SFTP session, at the command prompt, enter:

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
