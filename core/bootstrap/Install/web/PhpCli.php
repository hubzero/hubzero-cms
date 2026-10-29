<?php

/**
 * The PHP command line the web installer runs its commands with
 *
 * Used by both the bootstrap installer, for Composer, and the main web
 * installer, for the migrations.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web;

class PhpCli
{
    /**
     * The PHP command line to run a command with
     *
     * PHP_BINARY is the program serving this page, which is not always a
     * command line: under PHP-FPM it is the php-fpm daemon, and under
     * FrankenPHP it is empty. Nor can a script be left to its own first
     * line, #!/usr/bin/env php: a PHP-FPM worker has an empty environment,
     * so Composer started on the system's default path and then failed in
     * its post-install script with "env: 'php': No such file or directory",
     * and the install was reported as failed although it had worked. So the
     * PHP is named here: FrankenPHP's own command line, or the CLI of the PHP
     * version serving this page.
     *
     * @return  string  A command ready for the shell, or '' if there is none
     */
    public static function find(): string
    {
        if (PHP_SAPI === 'cli' && PHP_BINARY !== '') {
            return escapeshellarg(PHP_BINARY);
        }

        // FrankenPHP is its own command line, as frankenphp php-cli
        if (PHP_SAPI === 'frankenphp') {
            $self = PHP_BINARY !== '' ? PHP_BINARY : (string) @readlink('/proc/self/exe');

            if ($self !== '' && is_executable($self)) {
                return escapeshellarg($self) . ' php-cli';
            }
        }

        $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $candidates = [
            PHP_BINDIR . '/php' . $version,
            PHP_BINDIR . '/php',
            '/usr/bin/php' . $version,
            '/usr/local/bin/php' . $version,
            '/usr/bin/php',
            '/usr/local/bin/php',
        ];

        foreach (array_unique($candidates) as $candidate) {
            if (is_file($candidate) && is_executable($candidate)) {
                return escapeshellarg($candidate);
            }
        }

        return '';
    }

    /**
     * The environment a command runs in
     *
     * What getenv() reports under PHP-FPM is the web server's request, not
     * the worker's own environment, which is empty; a child process gets the
     * latter. So the PATH and a home Composer can write its cache to are
     * passed explicitly.
     *
     * @return  array
     */
    public static function environment(): array
    {
        $path = (string) getenv('PATH');

        if ($path === '') {
            $path = '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin';
        }

        $composerHome = (string) getenv('COMPOSER_HOME');

        if ($composerHome === '') {
            $composerHome = sys_get_temp_dir() . '/hubzero-composer';
        }

        $home = (string) getenv('HOME');

        return [
            'PATH'          => $path,
            'HOME'          => $home !== '' && is_writable($home) ? $home : $composerHome,
            'COMPOSER_HOME' => $composerHome,
        ];
    }
}
