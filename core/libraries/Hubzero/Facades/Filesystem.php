<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Filesystem facade
 *
 * @method static bool   exists(string $path)
 * @method static string read(string $path)
 * @method static bool   write(string $path, string $contents)
 * @method static bool   append(string $path, string $data)
 * @method static bool   delete(string $path)
 * @method static bool   move(string $path, string $target)
 * @method static bool   copy(string $path, string $target)
 * @method static bool   rename(string $path, string $target)
 * @method static bool   upload(string $path, string $target)
 * @method static mixed  find(mixed $paths, string $file)
 * @method static string name(string $path)
 * @method static string extension(string $path)
 * @method static string type(string $path)
 * @method static int    size(string $path)
 * @method static string mimetype(string $path)
 * @method static int    lastModified(string $path)
 * @method static bool   isDirectory(string $directory)
 * @method static bool   isWritable(string $path)
 * @method static bool   isFile(string $file)
 * @method static bool   isSafe(string $file)
 * @method static array  listContents(string $path, string $filter = '.', bool $recursive = false, bool $full = false, array $exclude = [])
 * @method static bool   makeDirectory(string $path, int $mode = 0755, bool $recursive = true, bool $force = false)
 * @method static bool   copyDirectory(string $path, string $target, int $options = null)
 * @method static bool   deleteDirectory(string $path, bool $preserve = false)
 * @method static string clean(string $file)
 * @method static string cleanPath(string $path)
 *
 * @codeCoverageIgnore
 */
class Filesystem extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'filesystem';
    }
}
