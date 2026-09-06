<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\System\PrivilegeManager;

/**
 * Console Privilege Notifier
 *
 * Handles display of privilege-related warnings and prompts
 * in the CLI environment.
 *
 * This class separates UI/output concerns from the core privilege
 * management logic in PrivilegeManager.
 */
class PrivilegeNotifier
{
    /**
     * The privilege manager instance
     */
    private PrivilegeManager $privileges;

    /**
     * Whether to use ANSI colors
     */
    private bool $ansi;

    /**
     * Constructor
     *
     * @param  PrivilegeManager|null  $privileges  Privilege manager (uses singleton if null)
     * @param  bool                   $ansi        Whether to use ANSI colors
     */
    public function __construct(?PrivilegeManager $privileges = null, bool $ansi = true)
    {
        $this->privileges = $privileges ?? PrivilegeManager::getInstance();
        $this->ansi = $ansi;
    }

    /**
     * Handle privilege detection on startup
     *
     * This should be called early in muse startup.
     * It handles:
     * - Dropping privileges if running via sudo
     * - Warning and prompting if running as root directly
     *
     * @return bool  True to continue, false to abort
     */
    public function handleStartup(): bool
    {
        if ($this->privileges->isSudo()) {
            return $this->handleSudoEnvironment();
        }

        if ($this->privileges->isRoot()) {
            return $this->handleRootEnvironment();
        }

        // Regular user - nothing special needed
        return true;
    }

    /**
     * Handle sudo environment
     *
     * Drops to original user and displays notice.
     *
     * @return bool  Always returns true (continue execution)
     */
    private function handleSudoEnvironment(): bool
    {
        // Drop to the original sudo user
        $this->privileges->initializeSudoEnvironment();

        // Display notice
        $sudoUser = $this->privileges->getSudoUser();

        $this->output("\n");

        if ($this->ansi) {
            $this->output("\033[31mSudo Environment Detected:\033[39m ");
            $this->output("Now running as user '\033[32m{$sudoUser}\033[39m'.\n");
        } else {
            $this->output("Sudo Environment Detected: ");
            $this->output("Now running as user '{$sudoUser}'.\n");
        }

        $this->output("Administrative privileges will only be used when necessary.\n");
        $this->output("\n");

        return true;
    }

    /**
     * Handle direct root environment
     *
     * Displays warning and prompts for confirmation.
     *
     * @return bool  True if user confirms, false to abort
     */
    private function handleRootEnvironment(): bool
    {
        $this->displayRootWarning();
        return $this->promptRootConfirmation();
    }

    /**
     * Display the root warning message
     *
     * @return void
     */
    public function displayRootWarning(): void
    {
        $this->output("\n");

        if ($this->ansi) {
            $this->output("\033[31m");
            $this->output(str_repeat("\u{2588}", 2) . " Security Warning:\033[39m\n");
        } else {
            $this->output("!! Security Warning:\n");
        }

        $this->output("You are running this installer with full administrative (root) privileges.\n");
        $this->output("Using root for installations can lead to security vulnerabilities.\n");
        $this->output("Consider using sudo to run the installer as a standard user instead.\n");
        $this->output("\n");
    }

    /**
     * Prompt user to confirm running as root
     *
     * @return bool  True if confirmed, false to abort
     */
    public function promptRootConfirmation(): bool
    {
        // Nobody is there to answer. Asking would either abort the run on an
        // empty read or block forever on an open pipe, so carry on: cron jobs
        // and scripted callers running as root worked before this prompt
        // existed, and the warning above is already in their log.
        if (!$this->isInteractive()) {
            $this->output("Not running interactively; continuing as root.\n\n");

            return true;
        }

        while (true) {
            $this->output("Continue anyway? [y/N] ");

            $line = fgets(STDIN);

            // Input closed underneath us part way through
            if ($line === false) {
                $this->output("\n");

                return false;
            }

            $response = strtolower(trim($line));

            if ($response === 'y' || $response === 'yes') {
                $this->output("\n");
                return true;
            }

            if ($response === 'n' || $response === 'no' || $response === '') {
                $this->output("\n");
                return false;
            }

            $this->output("Invalid response. Please enter y/yes or n/no.\n\n");
        }
    }

    /**
     * Whether there is a person on the other end of stdin
     *
     * False for cron, pipes, redirects and anything passing --no-interaction.
     *
     * @return bool
     */
    public function isInteractive(): bool
    {
        foreach (['--no-interaction', '--no-interactive', '-n'] as $flag) {
            if (in_array($flag, (array) ($_SERVER['argv'] ?? []), true)) {
                return false;
            }
        }

        if (!defined('STDIN')) {
            return false;
        }

        if (function_exists('stream_isatty')) {
            return stream_isatty(STDIN);
        }

        if (function_exists('posix_isatty')) {
            return @posix_isatty(STDIN);
        }

        return false;
    }

    /**
     * Display a message about privilege escalation
     *
     * @param  string  $action  What action is being performed
     * @return void
     */
    public function notifyEscalating(string $action = ''): void
    {
        if ($this->ansi) {
            $this->output("\033[33m[sudo]\033[39m ");
        } else {
            $this->output("[sudo] ");
        }

        if ($action !== '') {
            $this->output("Escalating privileges for: {$action}\n");
        } else {
            $this->output("Escalating to root privileges...\n");
        }
    }

    /**
     * Display a message about dropping privileges
     *
     * @return void
     */
    public function notifyDropping(): void
    {
        $sudoUser = $this->privileges->getSudoUser();

        if ($this->ansi) {
            $this->output("\033[32m[done]\033[39m ");
        } else {
            $this->output("[done] ");
        }

        if ($sudoUser !== null) {
            $this->output("Returning to user '{$sudoUser}'.\n");
        } else {
            $this->output("Dropping privileges.\n");
        }
    }

    /**
     * Set ANSI mode
     *
     * @param  bool  $ansi  Whether to use ANSI colors
     * @return self
     */
    public function setAnsi(bool $ansi): self
    {
        $this->ansi = $ansi;
        return $this;
    }

    /**
     * Output text
     *
     * @param  string  $text  Text to output
     * @return void
     */
    private function output(string $text): void
    {
        echo $text;
    }
}
