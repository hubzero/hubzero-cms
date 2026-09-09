<?php

/**
 * Git helper for safe git operations on data definition files.
 *
 * Replaces shell-concatenated git commands with properly escaped
 * proc_open calls.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Helpers;

class GitHelper
{
    /**
     * Initialize a git repository in the given directory.
     *
     * Creates the directory if it doesn't exist, then runs git init.
     *
     * @param   string  $dir  Absolute path to directory
     * @return  bool
     */
    public static function initRepo(string $dir): bool
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0770, true);
        }

        $cmd = 'git init';
        return self::run($cmd, $dir);
    }

    /**
     * Add and commit a file.
     *
     * @param   string  $repoDir   Path to git repository
     * @param   string  $filename  Filename (not path) to commit
     * @param   string  $message   Commit message
     * @param   string  $author    Author string "Name <email>"
     * @return  bool
     */
    public static function addAndCommit(
        string $repoDir,
        string $filename,
        string $message,
        string $author
    ): bool {
        $addCmd = 'git add ' . escapeshellarg($filename);
        self::run($addCmd, $repoDir);

        $commitCmd = 'git commit '
            . escapeshellarg($filename)
            . ' --author=' . escapeshellarg($author)
            . ' -m ' . escapeshellarg($message);

        return self::run($commitCmd, $repoDir);
    }

    /**
     * Commit a file (already tracked).
     *
     * @param   string  $repoDir   Path to git repository
     * @param   string  $filename  Filename to commit
     * @param   string  $message   Commit message
     * @param   string  $author    Author string
     * @return  bool
     */
    public static function commit(
        string $repoDir,
        string $filename,
        string $message,
        string $author
    ): bool {
        $cmd = 'git commit '
            . escapeshellarg($filename)
            . ' --author=' . escapeshellarg($author)
            . ' -m ' . escapeshellarg($message);

        return self::run($cmd, $repoDir);
    }

    /**
     * Get the current user's git author string.
     *
     * @return  string  "Name <email>"
     */
    public static function getAuthor(): string
    {
        return \Hubzero\Facades\User::get('name')
            . ' <' . \Hubzero\Facades\User::get('email') . '>';
    }

    /**
     * Run a shell command in a given directory.
     *
     * @param   string  $cmd  Command to run
     * @param   string  $cwd  Working directory
     * @return  bool    True if exit code was 0
     */
    private static function run(string $cmd, string $cwd): bool
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes, $cwd);
        if (!is_resource($process)) {
            return false;
        }

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return proc_close($process) === 0;
    }
}
