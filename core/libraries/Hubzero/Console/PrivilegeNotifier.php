<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\System\PrivilegeManager;

/**
 * Tells the operator what muse is doing about privileges at startup.
 *
 * Everything this class writes is a notice to a human, never part of a
 * command's result, so it goes to stderr (or the terminal for an interactive
 * prompt) and stays out of stdout, which callers such as com_installer parse
 * as JSON.
 */
class PrivilegeNotifier
{
    /**
     * Privilege manager
     *
     * @var  PrivilegeManager
     */
    private PrivilegeManager $privileges;

    /**
     * Whether to colour output, or null to decide from the stream
     *
     * @var  bool|null
     */
    private ?bool $ansi;

    /**
     * Stream notices are written to
     *
     * @var  resource|null
     */
    private $stream = null;

    /**
     * Constructor
     *
     * @param   PrivilegeManager|null  $privileges
     * @param   bool|null              $ansi        Colour output; null picks by whether the stream is a terminal
     */
    public function __construct(?PrivilegeManager $privileges = null, ?bool $ansi = null)
    {
        $this->privileges = $privileges ?? PrivilegeManager::getInstance();
        $this->ansi = $ansi;
    }

    /**
     * Handle privilege setup at startup
     *
     * This should be called early in muse startup.
     * It handles:
     * - Dropping privileges if running as root via sudo
     * - Warning and prompting if running as root directly
     *
     * @return bool  True to continue, false to abort
     */
    public function handleStartup(): bool
    {
        // Only sudo to root has privileges to drop. "sudo -u hubadmin" also
        // sets SUDO_USER, but a non-root process cannot seteuid back to the
        // caller, so it simply runs as the target user.
        if ($this->privileges->isSudo() && $this->privileges->isRoot()) {
            return $this->handleSudoEnvironment();
        }

        if ($this->privileges->isRoot()) {
            return $this->handleRootEnvironment();
        }

        // Regular user - nothing special needed
        return true;
    }

    /**
     * Drop to the original sudo user, or treat the process as plain root
     * when that is not possible
     *
     * @return bool  True to continue, false to abort
     */
    private function handleSudoEnvironment(): bool
    {
        // The drop fails when SUDO_USER cannot be resolved (deleted account,
        // directory service down). We are still root then, and saying
        // otherwise would be worse than the root warning.
        if (!$this->privileges->initializeSudoEnvironment()) {
            return $this->handleRootEnvironment();
        }

        $sudoUser = $this->privileges->getSudoUser();

        $this->output("\n");
        if ($this->ansi()) {
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
     * Warn about running as root and, when someone is there, ask
     *
     * @return bool  True to continue, false to abort
     */
    private function handleRootEnvironment(): bool
    {
        // A human is being asked a question: put it where they are looking,
        // even if they redirected stderr. Unsolicited notices stay on stderr.
        $interactive = $this->isInteractive();
        $saved = $this->stream;

        if ($interactive && ($tty = @fopen('/dev/tty', 'w'))) {
            $this->stream = $tty;
        }

        try {
            $this->displayRootWarning();
            return $this->promptRootConfirmation();
        } finally {
            if ($this->stream !== $saved) {
                fclose($this->stream);
                $this->stream = $saved;
            }
        }
    }

    /**
     * Display the root security warning
     *
     * @return void
     */
    public function displayRootWarning(): void
    {
        $this->output("\n");
        if ($this->ansi()) {
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
     * Ask whether to continue as root
     *
     * @return bool  True to continue, false to abort
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
     * Is someone there to answer a prompt?
     *
     * @return bool
     */
    public function isInteractive(): bool
    {
        foreach (['--no-interaction', '--no-interactive', '--non-interactive', '-n'] as $flag) {
            if (in_array($flag, (array) ($_SERVER['argv'] ?? []), true)) {
                return false;
            }
        }

        if (!defined('STDIN')) {
            return false;
        }

        return $this->isTty(STDIN);
    }

    /**
     * Notify that privileges are being escalated
     *
     * @param   string  $action  What the escalation is for
     * @return  void
     */
    public function notifyEscalating(string $action = ''): void
    {
        if ($this->ansi()) {
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
     * Notify that privileges are being dropped
     *
     * @return  void
     */
    public function notifyDropping(): void
    {
        $sudoUser = $this->privileges->getSudoUser();

        if ($this->ansi()) {
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
     * Set whether to colour output
     *
     * @param   bool  $ansi
     * @return  self
     */
    public function setAnsi(bool $ansi): self
    {
        $this->ansi = $ansi;
        return $this;
    }

    /**
     * Set the stream notices are written to
     *
     * @param   resource  $stream
     * @return  self
     */
    public function setStream($stream): self
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Whether to colour output
     *
     * Explicit setting first, then the console's own --no-colors flag, then
     * whether anyone is looking at a terminal: escape codes in apache's
     * error_log help nobody.
     *
     * @return bool
     */
    private function ansi(): bool
    {
        if ($this->ansi !== null) {
            return $this->ansi;
        }

        if (in_array('--no-colors', (array) ($_SERVER['argv'] ?? []), true)) {
            return false;
        }

        return $this->isTty($this->stream());
    }

    /**
     * Is the stream a terminal?
     *
     * @param   resource  $stream
     * @return  bool
     */
    private function isTty($stream): bool
    {
        if (!is_resource($stream)) {
            return false;
        }

        if (function_exists('stream_isatty')) {
            return stream_isatty($stream);
        }

        if (function_exists('posix_isatty')) {
            return @posix_isatty($stream);
        }

        return false;
    }

    /**
     * The stream notices go to: whatever was set, else stderr
     *
     * @return resource
     */
    private function stream()
    {
        if ($this->stream === null) {
            $this->stream = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');
        }

        return $this->stream;
    }

    /**
     * Output text
     *
     * Notices go to stderr so they never mix into a command's own output,
     * such as the JSON that com_installer parses from "--format=json".
     *
     * @param  string  $text  Text to output
     * @return void
     */
    private function output(string $text): void
    {
        fwrite($this->stream(), $text);
    }
}
