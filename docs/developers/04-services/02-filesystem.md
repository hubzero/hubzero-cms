<!--
status: rewritten
reviewed-against: 2.4-main @ a668500422
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/webdevs/services/filesystem
-->
# Filesystem

[`Hubzero\Filesystem\Filesystem`](../../../core/libraries/Hubzero/Filesystem/Filesystem.php)
is the file API. It holds an adapter, delegates every operation to it, and
adds a virus scan, path normalisation, and a macro mechanism. Because the
adapter is chosen at boot, the same calls work whether the hub writes to
local disk or over FTP.

## What the facade resolves to

<!--include: core/bootstrap/Site/Providers/FilesystemServiceProvider.php:29-56-->

With `ftp_enable` set in the global configuration the adapter is
[`Ftp`](../../../core/libraries/Hubzero/Filesystem/Adapter/Ftp.php),
configured from the `ftp_*` values; otherwise it is
[`Local`](../../../core/libraries/Hubzero/Filesystem/Adapter/Local.php),
constructed with the shell command used for virus scanning. A third
adapter, `None`, exists for tests. Four macros are registered on the way
out; see below.

> **Note:** There is no filesystem manager to go through, despite the name.
> `Hubzero\Filesystem\Manager` is an empty class left over from an earlier
> design. The `Filesystem` facade, or `App::get('filesystem')`, is the entry
> point.

> **Note:** Paths are real paths, not paths relative to some configured
> root. Build them from the [constants](../03-foundation/02-constants.md) —
> `PATH_APP . DS . 'site' . DS . 'wiki'` — and pass them whole.

## Reading and writing

```php
use Filesystem;

if (!Filesystem::exists($path))
{
    throw new Exception(Lang::txt('File not found.'));
}

$contents = Filesystem::read($path);
```

| Method | What it does |
|---|---|
| `exists($path)` | Whether the path exists |
| `read($path)` | File contents as a string; throws `FileNotFoundException` if absent |
| `write($path, $contents)` | Write, replacing what is there |
| `prepend($path, $data)` / `append($path, $data)` | Add to the start or end |
| `delete($path)` | Remove a file |
| `copy($path, $target)` / `rename($path, $target)` | Copy or rename; `move()` is an alias for `rename()` |
| `upload($path, $target)` | Move an uploaded temporary file into place |

`copy()` and `rename()` call `assertPresent()` on the source first, so a
missing source raises
[`FileNotFoundException`](../../../core/libraries/Hubzero/Filesystem/Exception/FileNotFoundException.php)
rather than returning `false`.

## Inspecting

| Method | Returns |
|---|---|
| `name($path)` | The filename without its extension |
| `extension($path)` | The extension, without the dot |
| `type($path)` | `file` or `dir` |
| `size($path)` | Size in bytes |
| `mimetype($path)` | The detected MIME type |
| `lastModified($path)` | Modification time |
| `isFile($path)` / `isDirectory($path)` / `isWritable($path)` | Booleans |
| `isSafe($path)` | Runs the configured virus scanner over the file |
| `find($paths, $file)` | Full path to `$file` in the first of `$paths` that has it, or `false` |

`isSafe()` is the one to remember. Everything a member uploads goes through
it, and a failure means the file is deleted rather than kept:

```php
if (!Filesystem::isSafe($path . DS . $file['name']))
{
    Filesystem::delete($path . DS . $file['name']);

    throw new Exception(Lang::txt('File rejected because the anti-virus scan failed.'));
}
```

## Directories

| Method | What it does |
|---|---|
| `makeDirectory($path, $mode = 0755, $recursive = true, $force = false)` | Create a directory |
| `deleteDirectory($path, $preserve = false)` | Remove it, optionally keeping the directory itself |
| `copyDirectory($path, $target, $options = null)` | Copy a tree |
| `setPermissions($path, $filemode = '0644', $foldermode = '0755')` | Chmod a tree |
| `listContents($path, $filter = '.', $recursive = false, $full = false, $exclude = [...])` | Entries as arrays with `type` and `path` |

## Cleaning names

Never build a path out of an uploaded filename without normalising it
first:

| Method | What it does |
|---|---|
| `clean($file)` | Strip a filename down to safe characters |
| `cleanPath($path)` | Normalise separators and collapse `..` |
| `cleanDirectory($directory)` | The same, for a directory name |

## Macros

A macro adds one method to the filesystem object. It implements
[`MacroInterface`](../../../core/libraries/Hubzero/Filesystem/MacroInterface.php)
— usually by extending `Hubzero\Filesystem\Macro\Base`, which supplies
`setFilesystem()` — and provides `getMethod()` and `handle()`:

<!--include: core/libraries/Hubzero/Filesystem/Macro/Files.php:13-50-->

`getMethod()` names the call; `handle()` receives whatever arguments the
call was given, with `$this->filesystem` already set. Register it and call
it:

```php
$filesystem = App::get('filesystem');

$filesystem->addMacro(new Example);

$content = $filesystem->examplify($path);
```

`addMacro()` refuses a macro without a `handle()` method, `hasMacro($name)`
tests for one, and calling a name that is neither a real method nor a
registered macro raises `BadMethodCallException`.

Four macros are registered at boot and are therefore always available on
the `Filesystem` facade:

| Call | Returns |
|---|---|
| `files($path, $filter, $recursive, $full, $exclude)` | Paths of the files in a directory |
| `directories($path, $filter, $recursive, $full, $exclude)` | Paths of the sub-directories |
| `emptyDirectory($path)` | Deletes everything inside a directory, keeping the directory |
| `directoryTree($path, $filter, $maxLevel, $level, $parent)` | A nested array describing the tree |

```php
$num_files = count(Filesystem::files(PATH_APP . DS . $folder));
```
