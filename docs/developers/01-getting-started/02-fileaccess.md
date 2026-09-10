<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/index/fileaccess
source-id: 3424
-->
# Accessing files

Two different problems share this name: getting at a hub's files on the
server, and reading and writing files from inside your code. This page
covers the first and points at the chapter that covers the second.

## Reaching the server

A hub runs on a Linux server and you work on it over SSH. From a terminal:

```bash
ssh username@hub.example.org
```

For file transfer, use sFTP, which runs over the same SSH connection and
encrypts commands and data alike. An ordinary FTP client cannot talk to it.

```bash
sftp username@hub.example.org
```

> **Warning:** A hub account does not carry shell access. Registering on a
> hub gives you a web account and nothing more; a system administrator has
> to grant SSH and sFTP separately.

Any current SSH client works. Windows ships OpenSSH, so `ssh` and `sftp` work
from PowerShell or the Command Prompt; [PuTTY](https://www.putty.org/) and
[WinSCP](https://winscp.net/) are the long-standing graphical alternatives.
macOS and Linux have `ssh` and `sftp` installed already.

Inside an `sftp` session, `get` and `put` transfer a file, `ls` and `cd` work
on the remote side, `lls` and `lcd` on the local side, and `help` lists the
rest. `scp -r` and `rsync -a` are usually quicker for a whole directory.

## Finding the hub

The web root is the hub's document root, conventionally `/www/<hubname>` on
a Hubzero server. Many sites keep a second, development copy alongside it,
often `/www/dev`. Both are ordinary directories; nothing in the CMS depends
on the name.

Underneath, the layout is the one described in
[Structure](../03-foundation/01-structure.md): `core/` holds the platform,
`app/` holds this hub's configuration, extensions, cache, and uploads, and
`index.php` at the root is the only entry point.

Two things to know before you edit anything in place:

- Files must stay readable by the web server user, `apache` on Enterprise
  Linux. A file you create over sFTP is owned by you.
- Everything under `app/` is a running hub's state and is not in the
  repository. Everything under `core/` is, so an edit there is a change you
  will have to carry forward or contribute back.

## Reading and writing files from code

Do not use PHP's `fopen` and `unlink` directly. The CMS has a file API, so
that the same calls work whether the hub writes to local disk or over FTP,
and so uploads are virus-scanned and paths are normalised on the way
through.

The entry point is the `Filesystem` facade, which resolves to
[`Hubzero\Filesystem\Filesystem`](../../../core/libraries/Hubzero/Filesystem/Filesystem.php):

```php
use Filesystem;

if (Filesystem::exists($path))
{
	$contents = Filesystem::read($path);
}

Filesystem::write($path, $contents);
Filesystem::makeDirectory($dir);
Filesystem::delete($path);
```

`exists()`, `read()`, `write()`, `append()`, `prepend()`, `copy()`,
`move()`, `rename()`, `delete()`, `upload()`, `makeDirectory()`,
`copyDirectory()`, `deleteDirectory()`, `isDirectory()`, `isFile()`,
`isWritable()`, `size()`, `mimetype()`, `lastModified()` and
`listContents()` are all on the class, along with four macros —
`files()`, `directories()`, `directoryTree()` and `emptyDirectory()` —
registered by the service provider at boot.

[Filesystem](../04-services/02-filesystem.md) is the full chapter: the
adapters, the macro mechanism, the safe-path rules, and the archive types.

> **Note:** The facade is a root-namespace alias. A namespaced file that
> writes `Filesystem::read()` without a `use Filesystem;` resolves the name
> inside its own namespace and fatals. See
> [Facades](../03-foundation/04-facades.md#importing-a-facade).
