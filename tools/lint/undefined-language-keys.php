#!/usr/bin/env php
<?php

/**
 * Find language keys the code asks for that no language file defines.
 *
 * Lang::txt() returns its argument unchanged when the key is not found, so a
 * missing string is not an error: the raw key is printed into the page. A
 * button reads COM_EVENTS_PAGES_REMOVED, a notice reads ALERTNOTAUTH. It
 * fails silently in exactly the places that get the least traffic, which is
 * why so many of these survive.
 *
 * Only literal keys are checked, read with PHP's tokenizer so a key inside a
 * comment or a concatenation is not counted. Keys built at runtime
 * ('COM_X_' . strtoupper($type)) cannot be checked this way and are skipped,
 * so a clean run does not prove every string resolves.
 *
 * A key counts as defined if any en-GB file under core/ or app/ defines it.
 * That is deliberately generous: Lang loads a handful of files per request,
 * not all of them, so a key defined only in some other extension's file may
 * still fail at runtime. Everything reported here is defined nowhere at all.
 *
 * Usage:
 *   php tools/lint/undefined-language-keys.php [--quiet] [path ...]
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$root = dirname(__DIR__, 2);
$defaults = ['core'];

$quiet = false;
$paths = [];
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--quiet') {
        $quiet = true;
    } elseif ($arg === '--help' || $arg === '-h') {
        fwrite(STDOUT, "usage: php tools/lint/undefined-language-keys.php [--quiet] [path ...]\n");
        exit(0);
    } else {
        $paths[] = $arg;
    }
}
$paths = $paths ?: $defaults;

/**
 * Every key defined by any en-GB language file in the tree.
 *
 * @param   string  $root  Repository root
 * @return  array<string,bool>
 */
function definedKeys($root)
{
    $keys = [];
    foreach (['core', 'app'] as $tree) {
        $base = $root . '/' . $tree;
        if (!is_dir($base)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() !== 'ini' || strpos($file->getPathname(), '/language/') === false) {
                continue;
            }
            foreach (file($file->getPathname(), FILE_IGNORE_NEW_LINES) as $line) {
                if (preg_match('/^([A-Z][A-Z0-9_]*)\s*=/', trim($line), $m)) {
                    $keys[$m[1]] = true;
                }
            }
        }
    }
    return $keys;
}

/**
 * Every literal key a file passes to Lang::txt() or Lang::txts().
 *
 * @param   string  $path  File to read
 * @return  array  List of [key, line]
 */
function requestedKeys($path)
{
    $tokens = @token_get_all(file_get_contents($path));
    if (!$tokens) {
        return [];
    }
    $found = [];
    $count = count($tokens);
    for ($i = 0; $i < $count - 4; $i++) {
        if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_STRING || $tokens[$i][1] !== 'Lang') {
            continue;
        }
        $j = $i + 1;
        if (!is_array($tokens[$j]) || $tokens[$j][0] !== T_DOUBLE_COLON) {
            continue;
        }
        $j++;
        if (!is_array($tokens[$j]) || !in_array($tokens[$j][1], ['txt', 'txts'], true)) {
            continue;
        }
        $j++;
        if (!is_string($tokens[$j]) || $tokens[$j] !== '(') {
            continue;
        }
        $j++;
        if (!is_array($tokens[$j]) || $tokens[$j][0] !== T_CONSTANT_ENCAPSED_STRING) {
            continue;                                   // a variable or a concatenation
        }
        $next = $tokens[$j + 1] ?? null;
        if (!is_string($next) || ($next !== ')' && $next !== ',')) {
            continue;                                   // concatenated onto something
        }
        $literal = trim($tokens[$j][1], "'\"");
        if (preg_match('/^[A-Z][A-Z0-9_]{2,}$/', $literal)) {
            $found[] = [$literal, $tokens[$j][2]];
        }
    }
    return $found;
}

$defined = definedKeys($root);
if (!$defined) {
    fwrite(STDERR, "no language files found\n");
    exit(2);
}

$missing = [];
foreach ($paths as $spec) {
    $base = $root . '/' . trim($spec, '/');
    $files = [];
    if (is_file($base)) {
        $files[] = $base;
    } elseif (is_dir($base)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php' && strpos($file->getPathname(), '/vendor/') === false) {
                $files[] = $file->getPathname();
            }
        }
    } else {
        fwrite(STDERR, "skipping missing path: $spec\n");
        continue;
    }
    sort($files);
    foreach ($files as $path) {
        foreach (requestedKeys($path) as [$key, $line]) {
            if (!isset($defined[$key])) {
                $missing[$key][] = substr($path, strlen($root) + 1) . ':' . $line;
            }
        }
    }
}

ksort($missing);
$references = 0;
foreach ($missing as $key => $sites) {
    $references += count($sites);
    if (!$quiet) {
        echo $key, ' (', count($sites), "):\n";
        foreach (array_slice($sites, 0, 3) as $site) {
            echo '    ', $site, "\n";
        }
        if (count($sites) > 3) {
            echo '    and ', count($sites) - 3, " more\n";
        }
    }
}

echo count($missing), " key(s) defined nowhere, $references reference(s)\n";
exit($missing ? 1 : 0);
