#!/usr/bin/env php
<?php

/**
 * Find namespaced files that call a global facade without importing it.
 *
 * The CMS registers its facades as root-namespace aliases (see
 * core/bootstrap/<client>/aliases.php): Route, App, Event, User, and the rest.
 * Inside a namespaced file an unqualified `Route::url()` resolves to
 * `Current\Namespace\Route`, not to the alias, so the call is a fatal
 * "class not found" unless the file carries `use Route;` or writes
 * `\Route::url()`.
 *
 * Nothing catches this before the line runs: the file parses, autoloading
 * finds nothing to complain about, and the failure only appears when that
 * branch executes. Rarely used error paths can carry the fault for years.
 *
 * This uses PHP's own tokenizer rather than pattern matching, so a facade
 * name inside a comment, a string, or a heredoc is not counted, and a name
 * the file declares itself or that another file declares in the same
 * namespace is left alone.
 *
 * Usage:
 *   php tools/lint/missing-facade-imports.php [--fix] [--quiet] [path ...]
 *
 * With no path it scans core/components, core/plugins, core/modules, and
 * core/libraries/Hubzero. It exits non-zero when anything is found, so it
 * can gate a build; --fix inserts the missing `use` statements instead and
 * exits zero.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$root = dirname(__DIR__, 2);
$defaults = ['core/components', 'core/plugins', 'core/modules', 'core/libraries/Hubzero'];

$fix = false;
$quiet = false;
$paths = [];
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--fix') {
        $fix = true;
    } elseif ($arg === '--quiet') {
        $quiet = true;
    } elseif ($arg === '--help' || $arg === '-h') {
        fwrite(STDOUT, "usage: php tools/lint/missing-facade-imports.php [--fix] [--quiet] [path ...]\n");
        exit(0);
    } else {
        $paths[] = $arg;
    }
}
$paths = $paths ?: $defaults;

/**
 * Every root-namespace alias the bootstrap registers.
 *
 * @param   string  $root  Repository root
 * @return  array<string,bool>
 */
function facadeNames($root)
{
    $names = [];
    foreach (glob($root . '/core/bootstrap/*/aliases.php') as $file) {
        if (preg_match_all("/'(\\w+)'\\s*=>\\s*'[\\w\\\\]+'/", file_get_contents($file), $m)) {
            foreach ($m[1] as $name) {
                $names[$name] = true;
            }
        }
    }
    return $names;
}

/**
 * Every PHP file under the given paths, vendor directories excluded.
 *
 * @param   string  $root   Repository root
 * @param   array   $paths  Repo-relative paths
 * @return  array
 */
function phpFiles($root, array $paths)
{
    $files = [];
    foreach ($paths as $spec) {
        $base = $root . '/' . trim($spec, '/');
        if (is_file($base)) {
            $files[] = $base;
            continue;
        }
        if (!is_dir($base)) {
            fwrite(STDERR, "skipping missing path: $spec\n");
            continue;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php' && strpos($file->getPathname(), '/vendor/') === false) {
                $files[] = $file->getPathname();
            }
        }
    }
    $files = array_unique($files);
    sort($files);
    return $files;
}

/**
 * Read one file's namespace, imported short names, declared classes, and
 * the unqualified static calls it makes to a facade name.
 *
 * @param   string  $path     File to read
 * @param   array   $facades  Facade names to look for
 * @return  array|null
 */
function inspect($path, array $facades)
{
    $source = file_get_contents($path);
    if ($source === false) {
        return null;
    }
    $tokens = @token_get_all($source);
    if (!$tokens) {
        return null;
    }

    $namespace = '';
    $uses      = [];
    $declared  = [];
    $calls     = [];
    $count     = count($tokens);

    $nameTokens = [T_STRING, T_NS_SEPARATOR];
    foreach (['T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE'] as $constant) {
        if (defined($constant)) {
            $nameTokens[] = constant($constant);
        }
    }

    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];
        if (!is_array($token)) {
            continue;
        }

        if ($token[0] === T_NAMESPACE) {
            $buffer = '';
            for ($j = $i + 1; $j < $count; $j++) {
                if (is_string($tokens[$j]) && ($tokens[$j] === ';' || $tokens[$j] === '{')) {
                    break;
                }
                if (is_array($tokens[$j]) && in_array($tokens[$j][0], $nameTokens, true)) {
                    $buffer .= $tokens[$j][1];
                }
            }
            $namespace = trim($buffer, '\\');
            continue;
        }

        if ($token[0] === T_USE) {
            $buffer   = '';
            $alias    = null;
            $isClosure = false;
            for ($j = $i + 1; $j < $count; $j++) {
                if (is_string($tokens[$j])) {
                    if ($tokens[$j] === '(') {          // closure: use ($var)
                        $isClosure = true;
                        break;
                    }
                    if ($tokens[$j] === ';' || $tokens[$j] === '{') {
                        break;
                    }
                    if ($tokens[$j] === ',') {          // grouped: use A, B;
                        if ($buffer !== '') {
                            $parts = explode('\\', trim($buffer, '\\'));
                            $uses[$alias ?: end($parts)] = true;
                        }
                        $buffer = '';
                        $alias  = null;
                    }
                    continue;
                }
                if ($tokens[$j][0] === T_AS) {
                    for ($k = $j + 1; $k < $count; $k++) {
                        if (is_array($tokens[$k]) && $tokens[$k][0] === T_STRING) {
                            $alias = $tokens[$k][1];
                            break;
                        }
                    }
                    while ($j < $count && !(is_string($tokens[$j]) && ($tokens[$j] === ';' || $tokens[$j] === ','))) {
                        $j++;
                    }
                    $j--;
                    continue;
                }
                if (in_array($tokens[$j][0], $nameTokens, true)) {
                    $buffer .= $tokens[$j][1];
                }
            }
            if (!$isClosure && $buffer !== '') {
                $parts = explode('\\', trim($buffer, '\\'));
                $uses[$alias ?: end($parts)] = true;
            }
            continue;
        }

        $isTypeKeyword = in_array($token[0], [T_CLASS, T_INTERFACE, T_TRAIT], true)
            || (defined('T_ENUM') && $token[0] === T_ENUM);
        if ($isTypeKeyword) {
            for ($j = $i + 1; $j < $count; $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $declared[$tokens[$j][1]] = true;
                    break;
                }
                if (is_string($tokens[$j]) && ($tokens[$j] === '(' || $tokens[$j] === '{')) {
                    break;                              // anonymous class, or ::class
                }
            }
            continue;
        }

        if ($token[0] === T_DOUBLE_COLON) {
            $before = $i - 1;
            while ($before >= 0 && is_array($tokens[$before]) && $tokens[$before][0] === T_WHITESPACE) {
                $before--;
            }
            if ($before < 0 || !is_array($tokens[$before]) || $tokens[$before][0] !== T_STRING) {
                continue;                               // qualified name, variable, or static::
            }
            $name = $tokens[$before][1];
            if (!isset($facades[$name])) {
                continue;
            }
            $prior = $before - 1;
            while ($prior >= 0 && is_array($tokens[$prior]) && $tokens[$prior][0] === T_WHITESPACE) {
                $prior--;
            }
            $qualified = $prior >= 0 && is_array($tokens[$prior])
                && in_array($tokens[$prior][0], [T_NS_SEPARATOR, T_OBJECT_OPERATOR, T_DOUBLE_COLON], true);
            if (!$qualified) {
                $calls[$name] = isset($calls[$name]) ? $calls[$name] + 1 : 1;
            }
        }
    }

    return [
        'namespace' => $namespace,
        'uses'      => $uses,
        'declared'  => $declared,
        'calls'     => $calls,
    ];
}

/**
 * Insert the missing use statements after the file's existing use block.
 *
 * @param   string  $path     File to edit
 * @param   array   $missing  Facade names to import
 * @return  boolean
 */
function addImports($path, array $missing)
{
    $source = file_get_contents($path);
    $lines  = preg_split('/\R/', $source);
    $eol    = strpos($source, "\r\n") !== false ? "\r\n" : "\n";

    $lastUse   = null;
    $namespace = null;
    $depth     = 0;
    foreach ($lines as $index => $line) {
        if ($namespace === null && preg_match('/^\s*namespace\s+[^;]+;/', $line)) {
            $namespace = $index;
        }
        if ($depth === 0 && preg_match('/^\s*use\s+[^(]+;/', $line)) {
            $lastUse = $index;
        }
        $depth += substr_count($line, '{') - substr_count($line, '}');
        if ($depth > 0 && $lastUse !== null) {
            break;                                      // past the header
        }
    }

    $at = $lastUse !== null ? $lastUse : $namespace;
    if ($at === null) {
        return false;
    }

    $additions = [];
    foreach ($missing as $name => $unused) {
        $additions[] = 'use ' . $name . ';';
    }
    if ($lastUse === null) {
        $additions = array_merge([''], $additions);
    }

    array_splice($lines, $at + 1, 0, $additions);
    file_put_contents($path, implode($eol, $lines));
    return true;
}

// ---------------------------------------------------------------- run

$facades = facadeNames($root);
if (!$facades) {
    fwrite(STDERR, "no aliases found under core/bootstrap/*/aliases.php\n");
    exit(2);
}

$files = phpFiles($root, $paths);

// Pass one: what every namespace declares, so a component's own Config or
// User class is not mistaken for the facade of that name.
$inspected  = [];
$namespaced = [];
foreach ($files as $path) {
    $info = inspect($path, $facades);
    if ($info === null || $info['namespace'] === '') {
        continue;                                       // root namespace: aliases resolve
    }
    $inspected[$path] = $info;
    foreach ($info['declared'] as $name => $unused) {
        $namespaced[$info['namespace']][$name] = true;
    }
}

// Pass two: report.
$totalNames = 0;
$totalFiles = 0;
$fixedFiles = 0;
foreach ($inspected as $path => $info) {
    $missing = [];
    foreach ($info['calls'] as $name => $count) {
        if (isset($info['uses'][$name]) || isset($info['declared'][$name])) {
            continue;
        }
        if (isset($namespaced[$info['namespace']][$name])) {
            continue;
        }
        $missing[$name] = $count;
    }
    if (!$missing) {
        continue;
    }
    ksort($missing);
    $totalFiles++;
    $totalNames += count($missing);

    if (!$quiet) {
        $detail = [];
        foreach ($missing as $name => $count) {
            $detail[] = $name . ' (' . $count . ')';
        }
        echo substr($path, strlen($root) + 1), ': ', implode(', ', $detail), "\n";
    }
    if ($fix && addImports($path, $missing)) {
        $fixedFiles++;
    }
}

if ($fix) {
    echo "added $totalNames import(s) to $fixedFiles file(s)\n";
    exit(0);
}
echo "found $totalNames missing facade import(s) across $totalFiles file(s)\n";
exit($totalNames ? 1 : 0);
