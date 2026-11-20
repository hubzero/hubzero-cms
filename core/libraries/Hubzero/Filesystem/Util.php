<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

use Hubzero\Filesystem\Util\MimeType;
use Hubzero\Filesystem\Exception\PathViolationException;

/**
 * Utility methods
 */
class Util
{
    /**
     * Normalize a dirname return value.
     *
     * @param   string  $dirname
     * @return  string  Normalized dirname
     */
    public static function normalizeDirname($dirname)
    {
        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }

    /**
     * Get a normalized dirname from a path.
     *
     * @param   string  $path
     * @return  string  Dirname
     */
    public static function dirname($path)
    {
        return static::normalizeDirname(dirname($path));
    }

    /**
     * Checks for snooping outside of the file system root.
     *
     * @param   string  $path  A file system path to check.
     * @param   string  $ds    Directory separator (optional).
     * @return  string  A cleaned version of the path or exit on error.
     */
    public static function checkPath($path, $ds = DIRECTORY_SEPARATOR)
    {
        if (str_contains($path, '..')) {
            throw new PathViolationException('Use of relative paths not permitted');
        }

        $path = self::normalizePath($path);
        if (
            PATH_ROOT != '' && !str_starts_with(
                $path,
                self::normalizePath(PATH_ROOT),
            )  && !str_starts_with($path, self::normalizePath(PATH_CORE))
        ) {
            // Don't translate
            throw new PathViolationException('Snooping out of bounds @ ' . $path);
        }

        return $path;
    }

    /**
     * Normalize path.
     *
     * @param   mixed   $path  The path: a string, or nothing. Anything else is refused.
     * @param   mixed  $ds
     * @return  string
     * @throws  \InvalidArgumentException
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        if (!is_string($path) && !empty($path)) {
            throw new \InvalidArgumentException('$path is not a string.');
        }

        if (!is_string($path)) {
            // Only an empty value gets here: null, false, 0 or an empty array
            $path = is_scalar($path) ? (string) $path : '';
        }

        $path = trim($path);

        // Remove any kind of funky unicode whitespace
        $path = preg_replace('#\p{C}+|^\./#u', '', $path);

        if (empty($path)) {
            $path = PATH_ROOT;
        } elseif ($ds == '\\' && $path[0] == '\\' && $path[1] == '\\') {
            // Remove double slashes and backslashes and convert all slashes
            // and backslashes to DIRECTORY_SEPARATOR. If dealing with a UNC
            // path don't forget to prepend the path with a backslash.
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    /**
     * Normalize relative directories in a path.
     *
     * @param   string  $path
     * @return  string
     */
    public static function normalizeRelativePath($path)
    {
        // Path remove self referring paths ("/./").
        $path = (string) preg_replace('#/\.(?=/)|^\./|/\./?$#', '', $path);

        // Regex for resolving relative paths
        $regex = '#/*[^/\.]+/\.\.#Uu';

        while (preg_match($regex, (string) $path)) {
            $path = (string) preg_replace($regex, '', (string) $path);
        }

        return $path;
    }

    /**
     * Makes path name safe to use.
     *
     * @param   string  $path  The full path to sanitise.
     * @return  string  The sanitised string.
     */
    public static function normalizeDirectory($path)
    {
        $regex = ['#[^A-Za-z0-9:_\\\/-]#'];

        return (string) preg_replace($regex, '', $path);
    }

    /**
     * Normalize path.
     *
     * @param   string  $file
     * @return  string
     */
    public static function normalizeFile($file)
    {
        // Remove any trailing dots, as those aren't ever valid file names.
        $normalized = rtrim($file, '.');

        $regex = [
            '#(\.){2,}#',
            '#[^A-Za-z0-9\.\_\- ]#',
            '#^\.#',
        ];

        return (string) preg_replace($regex, '', $normalized);
    }

    /**
     * Save the raw request body to a file
     *
     * The AJAX uploaders send a file as the request body; each controller
     * had its own copy of this, none of which checked that the streams
     * opened.
     *
     * @param   string  $file  Where to write it
     * @return  bool    False when the body, a temporary file or the target
     *                  could not be opened, or a copy failed
     */
    public static function saveRequestBody($file)
    {
        $input = fopen('php://input', 'r');
        $temp  = tmpfile();

        if ($input === false || $temp === false) {
            if ($input !== false) {
                fclose($input);
            }
            if ($temp !== false) {
                fclose($temp);
            }
            return false;
        }

        $copied = stream_copy_to_stream($input, $temp);
        fclose($input);

        $target = $copied === false ? false : fopen($file, 'w');
        if ($target === false) {
            fclose($temp);
            return false;
        }

        fseek($temp, 0, SEEK_SET);
        $written = stream_copy_to_stream($temp, $target);
        fclose($target);
        fclose($temp);

        return $written !== false;
    }

    /**
     * Get content size.
     *
     * @param   string   $contents
     * @return  integer  Content size
     */
    public static function contentSize($contents)
    {
        return mb_strlen($contents, '8bit');
    }

    /**
     * Guess MIME Type based on the path of the file and it's content.
     *
     * @param   string  $path
     * @param   string  $content
     * @return  mixed   MIME Type or NULL if no extension detected
     */
    public static function guessMimeType($path, $content)
    {
        $mimeType = MimeType::detectByContent($content);

        if (empty($mimeType) || $mimeType === 'text/plain') {
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if ($extension) {
                $mimeType = MimeType::detectByFileExtension($extension) ?: 'text/plain';
            }
        }

        return $mimeType;
    }

    /**
     * The URL path, from the site root, of a file or directory inside core: '/core/components/com_x/admin' for
     * PATH_CORE/components/com_x/admin. Taking PATH_ROOT off the front of the path gave the full file system path
     * when core is not inside the document root (linked into it, or set with PATH_CORE).
     *
     * @param   string  $path  A path inside core
     * @return  string
     */
    public static function coreUrlPath($path)
    {
        $core = (string) realpath(PATH_CORE);
        $real = realpath($path);
        if ($real === false) {
            $real = $path;
        }

        $inside = ($core !== '' && str_starts_with($real, $core)) ? substr($real, strlen($core)) : '';

        return '/' . basename(PATH_CORE) . $inside;
    }

    /**
     * The URL path, from the site root, of a file or directory either under the document root or inside core,
     * wherever core is. For code that may run from either place, such as a group template copied into app.
     *
     * @param   string  $path  A path under PATH_ROOT or inside core
     * @return  string
     */
    public static function urlPath($path)
    {
        $root = (string) realpath(PATH_ROOT);
        $real = realpath($path);
        if ($real === false) {
            $real = $path;
        }

        // Inside core first: core is linked into the root on many hubs, and its
        // real path then lies outside it
        $core = (string) realpath(PATH_CORE);
        if ($core !== '' && str_starts_with($real, $core)) {
            return self::coreUrlPath($real);
        }

        if ($root !== '' && str_starts_with($real, $root)) {
            return '/' . ltrim(substr($real, strlen($root)), '/');
        }

        return self::coreUrlPath($path);
    }
}
