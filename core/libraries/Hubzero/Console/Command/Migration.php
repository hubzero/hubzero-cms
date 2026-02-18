<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Migration class
 **/
class Migration extends Base implements CommandInterface
{
    /**
     * Default (required) command - just executes run
     *
     * @return  void
     **/
    public function execute()
    {
        $this->run();
    }

    /**
     * Run migration
     *
     * @museDescription  Runs pending migrations according to options provided
     *
     * @return  void
     **/
    public function run()
    {
        // Direction, up or down
        $direction = 'up';
        if ($this->arguments->getOpt('d')) {
            if ($this->arguments->getOpt('d') == 'up' || $this->arguments->getOpt('d') == 'down') {
                $direction = $this->arguments->getOpt('d');
            } else {
                $this->output->error('Error: Direction must be one of "up" or "down"');
            }
        }

        // Overriding default document root?
        $directory = null;
        if ($this->arguments->getOpt('r')) {
            if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r'))) {
                $directory = rtrim($this->arguments->getOpt('r'), DS);
            } else {
                $this->output->error('Migration Error: Provided directory is not valid');
            }
        }

        // Migrating a super group
        $alternativeDatabase = null;
        if ($this->arguments->getOpt('group')) {
            $cname = $this->arguments->getOpt('group');
            $group = \Hubzero\User\Group::getInstance($cname);
            if ($group && $group->isSuperGroup()) {
                // Get group config
                $groupsConfig = \Component::params('com_groups');

                // Path to group folder
                $directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
                $directory .= DS . $group->get('gidNumber');

                // make sure we have migrations dir
                if (!is_dir($directory . DS . 'migrations') || !is_readable($directory . DS . 'migrations')) {
                    $this->output->error('Error: Migrations directory does not exist.');
                }

                // Get group database
                $alternativeDatabase = \Hubzero\User\Group\Helper::getDBO(array(), $group->get('cn'));

                // make sure we have a group db
                if ($alternativeDatabase->getErrorNum() > 0) {
                    $this->output->error('Error: Could not connect to Group Database.');
                }
            } else {
                $this->output->error('Error: Provided group is not valid');
            }
        }

        // Forcing update
        $force = false;
        if ($this->arguments->getOpt('force')) {
            if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file')) {
                $this->output->error(
                    'Error: You cannot specify the "force" option without ' .
                    'specifying a specific extention or file'
                );
            } else {
                $force = true;
            }
        }

        // Logging only - record migration
        $logOnly = false;
        if ($this->arguments->getOpt('m')) {
            if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file')) {
                $this->output->error(
                    'Error: You cannot specify the "Log only (-m)" option without ' .
                    'specifying a specific extention or file'
                );
            } else {
                $logOnly = true;
            }
        }

        // Ignore dates
        $listAll = false;
        if ($this->arguments->getOpt('a') || $this->arguments->getOpt('i')) {
            $listAll = true;
        }

        // Specific extension
        $extension = null;
        if ($this->arguments->getOpt('e')) {
            $pattern = '/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|' .
                '^plg_[[:alnum:]]+_[[:alnum:]]+$|^tpl_[[:alnum:]]+$|^core$/i';
            if (!preg_match($pattern, $this->arguments->getOpt('e'))) {
                $this->output->error(
                    'Error: extension should match the pattern of ' .
                    'com_*, mod_*, tpl_*, plg_*_*, or core'
                );
            } else {
                $extension = $this->arguments->getOpt('e');
            }
        }

        // Specific file
        $file = null;
        if ($this->arguments->getOpt('file')) {
            if (!preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $this->arguments->getOpt('file'))) {
                $this->output->error('Error: Provided filename does not appear to be valid');
            } else {
                $file = $this->arguments->getOpt('file');

                // Also force "ignore dates mode", as that's somewhat implied by giving a specific filename
                $listAll = true;
            }
        }

        // Target alias (prev, next, latest, first, current)
        if ($this->arguments->getOpt('target') && !$file) {
            $targetAlias = strtolower($this->arguments->getOpt('target'));
            $tempMigration = new \Hubzero\Content\Migration($directory);

            $resolved = $tempMigration->resolveAlias($targetAlias, $extension);

            if ($resolved === false) {
                $validAliases = $tempMigration->getValidAliases();
                $aliasHelp = implode(', ', array_keys($validAliases));
                $this->output->error("Error: Invalid target alias '{$targetAlias}'. Valid aliases: {$aliasHelp}");
            }

            if ($resolved['file'] === null) {
                $this->output->addLine($resolved['info'], 'warning');
                return;
            }

            $file = $resolved['file'];
            $listAll = true;

            $this->output->addLine("Resolved --target={$targetAlias} to: {$file}", 'info');
            $this->output->addLine("  ({$resolved['info']})", 'info');
            $this->output->addSpacer();
        }

        // Dryrun
        $dryrun = true;
        if ($this->arguments->getOpt('f')) {
            $dryrun = false;
        }

        // Email results
        $email = false;
        if ($this->arguments->getOpt('email')) {
            if (
                !preg_match('/^[a-zA-Z0-9\.\_\-]+@[a-zA-Z0-9\.]+\.[a-zA-Z]{2,
                4}$/', $this->arguments->getOpt('email'))
            ) {
                $this->output->error('Error: ' .
                    $this->arguments->getOpt('email') .
                    ' does not appear to be a valid email address');
            } else {
                $email = $this->arguments->getOpt('email');
            }
        }

        // Create migration object
        $migration = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

        // Search vendor directories?
        if ($this->arguments->getOpt('vendor')) {
            $vendorPath = PATH_APP . DS . 'vendor';

            if (is_dir($vendorPath)) {
                foreach (scandir($vendorPath) as $namespace) {
                    if ($namespace != '.' && $namespace != '..' && is_dir($vendorPath . DS . $namespace)) {
                        foreach (scandir($vendorPath . DS . $namespace) as $package) {
                            if (
                                $package != '.' && $package != '..' && is_dir($vendorPath .
                                DS .
                                $namespace .
                                DS .
                                $package)
                            ) {
                                $migrationPath = $vendorPath . DS . $namespace . DS . $package . DS . 'src';
                                if (is_dir($migrationPath . DS . 'migrations')) {
                                    $migration->addSearchPath($migrationPath);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Make sure we got a migration object
        if ($migration === false) {
            $this->output->error('Error: failed to instantiate new migration object.');
        }

        // All-or-nothing transaction mode
        if ($this->arguments->getOpt('all-or-nothing')) {
            $migration->setAllOrNothing(true);
        }

        if ($this->output->isInteractive()) {
            // Register callback function for adding lines interactively
            $output   = $this->output;
            $callback = function ($message, $type = null) use ($output) {
                $output->addLine($message, $type);
            };
            $migration->registerCallback('message', $callback);

            // Add progress callback as well
            $progress = $this->output->getProgressOutput();
            $migration->registerCallback('progress', $progress);
        }

        // Find migration files
        if ($migration->find($extension, $file) === false) {
            // Find failed, do nothing
            if (count($migration->get('log')) > 0) {
                $this->output->addLinesFromArray($migration->get('log'));
            }
            $this->output->error('Migration find failed! See log messages for details.');
        } else // no errors during 'find', so continue
        {
            // Run migration itself
            if (!$result = $migration->migrate($direction, $force, $dryrun, $listAll, $logOnly)) {
                if (count($migration->get('log')) > 0) {
                    $this->output->addLinesFromArray($migration->get('log'));
                }
                $this->output->error('Migration failed! See log messages for details.');
            } else {
                if (!$this->output->isInteractive()) {
                    if ($this->output->getMode() == 'minimal') {
                        if (count($migration->get('log')) > 0) {
                            $missed   = array();
                            $pending  = array();
                            $complete = array();
                            foreach ($migration->get('log') as $log) {
                                if (
                                    preg_match(
                                        '/would run up\(\) (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i',
                                        $log['message'],
                                        $matches
                                    )
                                ) {
                                    $pending[] = $matches[1] . $matches[2];
                                }
                                if (
                                    preg_match(
                                        '/completed up\(\) in (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i',
                                        $log['message'],
                                        $matches
                                    )
                                    ||
                                    preg_match(
                                        '/would ignore up\(\) (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i',
                                        $log['message'],
                                        $matches
                                    )
                                ) {
                                    $complete[] = $matches[1] . $matches[2];
                                }
                                if (
                                    preg_match('/migration up\(\) in (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php) has 
                                    not been run/i', $log['message'], $matches)
                                ) {
                                    $missed[] = $matches[1] . $matches[2];
                                }
                            }

                            if (count($pending) > 0) {
                                $this->output->addLine(array('pending'  => $pending));
                            }
                            if (count($missed) > 0) {
                                $this->output->addLine(array('missed'   => $missed));
                            }
                            if (count($complete) > 0) {
                                $this->output->addLine(array('complete' => $complete));
                            }
                        }
                    } else {
                        $this->output->addLinesFromArray($migration->get('log'));
                    }
                }

                // Final success message
                if ($this->output->getMode() != 'minimal') {
                    $this->output->addLine('Success: ' . ucfirst($direction) . ' migration complete!', 'success');
                }
            }
        }

        // Email results if requested (only do so if there's something to report)
        if ($email && count($migration->get('affectedFiles')) > 0) {
            $this->output->addLine("Emailing results to: {$email}");

            $headers = "From: Migrations <automator@" . php_uname("n") . ">";
            $subject = "Migration output - " . php_uname("n") . " [" . date("d-M-Y H:i:s") . "]";

            $message = "";
            foreach ($migration->get('log') as $line) {
                $message .= $line['message'] . "\n";
            }

            // Send the message
            if (!mail($email, $subject, $message, $headers)) {
                $this->output->addLine("Error: failed to send message!", 'warning');
            }
        } elseif ($email) {
            $this->output->addLine('Ignoring email as no files were affected in this run.', 'info');
        }
    }

    /**
     * Show migration status summary
     *
     * @museDescription  Shows a summary of migration status (pending, executed, failed)
     *
     * @return  void
     **/
    public function status()
    {
        // Overriding default document root?
        $directory = null;
        if ($this->arguments->getOpt('r')) {
            if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r'))) {
                $directory = rtrim($this->arguments->getOpt('r'), DS);
            } else {
                $this->output->error('Error: Provided directory is not valid');
            }
        }

        // Specific extension filter
        $extension = null;
        if ($this->arguments->getOpt('e')) {
            $pattern = '/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|' .
                '^plg_[[:alnum:]]+_[[:alnum:]]+$|^tpl_[[:alnum:]]+$|^core$/i';
            if (!preg_match($pattern, $this->arguments->getOpt('e'))) {
                $this->output->error(
                    'Error: extension should match the pattern of ' .
                    'com_*, mod_*, tpl_*, plg_*_*, or core'
                );
            } else {
                $extension = $this->arguments->getOpt('e');
            }
        }

        // Create migration object
        $migration = new \Hubzero\Content\Migration($directory);

        // Get status summary
        $status = $migration->getStatus($extension);

        if ($status === false) {
            $this->output->error('Error: Failed to retrieve migration status');
            return;
        }

        // Display summary header
        $this->output->addLine('');
        $this->output->addLine('Migration Status Summary', 'info');
        $this->output->addLine(str_repeat('=', 50));
        $this->output->addLine('');

        // Display counts
        $this->output->addLine(sprintf('  Available:  %d', $status['counts']['available']));
        $this->output->addLine(sprintf('  Executed:   %d', $status['counts']['executed']), 'success');
        $pendingStyle = $status['counts']['pending'] > 0 ? 'warning' : null;
        $failedStyle = $status['counts']['failed'] > 0 ? 'error' : null;
        $skippedStyle = $status['counts']['skipped'] > 0 ? 'warning' : null;
        $this->output->addLine(sprintf('  Pending:    %d', $status['counts']['pending']), $pendingStyle);
        $this->output->addLine(sprintf('  Failed:     %d', $status['counts']['failed']), $failedStyle);
        $this->output->addLine(sprintf('  Skipped:    %d', $status['counts']['skipped']), $skippedStyle);
        $this->output->addLine('');

        // Display last executed migration
        if ($status['last_executed']) {
            $this->output->addLine('Last Executed:', 'info');
            $lastExec = $status['last_executed'];
            $execTime = isset($lastExec->execution_time) && $lastExec->execution_time
                ? sprintf(' (%dms)', $lastExec->execution_time)
                : '';
            $this->output->addLine(sprintf('  %s %s%s', $lastExec->file, $lastExec->date, $execTime));
            $this->output->addLine('');
        }

        // Display next pending migration
        if ($status['next_pending']) {
            $this->output->addLine('Next Pending:', 'warning');
            $this->output->addLine(sprintf('  %s', $status['next_pending']));
            $this->output->addLine('');
        }

        // Show verbose list if requested
        if ($this->arguments->getOpt('v')) {
            // Pending migrations
            if (!empty($status['pending'])) {
                $this->output->addLine('Pending Migrations:', 'warning');
                foreach ($status['pending'] as $file) {
                    $this->output->addLine(sprintf('  - %s', $file));
                }
                $this->output->addLine('');
            }

            // Failed migrations
            if (!empty($status['failed'])) {
                $this->output->addLine('Failed Migrations:', 'error');
                foreach ($status['failed'] as $entry) {
                    $this->output->addLine(sprintf('  - %s/%s (%s)', $entry->scope, $entry->file, $entry->date));
                }
                $this->output->addLine('');
            }

            // Recent history (last 10)
            if (!empty($status['recent'])) {
                $this->output->addLine('Recent Executions (last 10):', 'info');
                $items = [['File', 'Direction', 'Status', 'Date', 'Time']];
                foreach ($status['recent'] as $entry) {
                    $execTime = isset($entry->execution_time) && $entry->execution_time
                        ? sprintf('%dms', $entry->execution_time)
                        : '-';
                    $items[] = [
                        $entry->file,
                        $entry->direction,
                        $entry->status ?? 'success',
                        $entry->date,
                        $execTime
                    ];
                }
                $this->output->addTable($items, true);
            }
        } else {
            $this->output->addLine('Use -v for detailed list of migrations');
        }
    }

    /**
     * Report migration run info
     *
     * @museDescription  Shows a history of previously run migrations
     *
     * @return  void
     **/
    public function history()
    {
        $migration = new \Hubzero\Content\Migration();
        $history   = $migration->history();
        $items     = [];

        if ($history && count($history) > 0) {
            // Check if execution_time column exists in results
            $hasExecTime = isset($history[0]->execution_time);

            $headers = ['File', 'By', 'Direction', 'Status', 'Date'];
            if ($hasExecTime) {
                $headers[] = 'Time';
            }
            $items[] = $headers;

            foreach ($history as $entry) {
                $row = [
                    $entry->scope . DS . $entry->file,
                    $entry->action_by,
                    $entry->direction,
                    $entry->status ?? 'success',
                    $entry->date
                ];

                if ($hasExecTime) {
                    $row[] = $entry->execution_time
                        ? sprintf('%dms', $entry->execution_time)
                        : '-';
                }

                $items[] = $row;
            }

            $this->output->addTable($items, true);
        } else {
            $this->output->addLine('No history to display.');
        }
    }

    /**
     * Mark a migration as executed (or remove a marking) without running it
     *
     * @museDescription  Marks a migration as executed without running it, or removes a tracking record
     *
     * @return  void
     **/
    public function mark()
    {
        // Determine action: --add or --delete
        $add = $this->arguments->getOpt('add');
        $delete = $this->arguments->getOpt('delete');

        if (!$add && !$delete) {
            $this->output->error('Error: You must specify either --add or --delete');
        }

        if ($add && $delete) {
            $this->output->error('Error: Cannot specify both --add and --delete');
        }

        // Require a specific file
        $file = $this->arguments->getOpt('file');
        if (!$file) {
            $this->output->error('Error: You must specify a migration file with --file');
        }

        // Validate filename format
        if (!preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $file)) {
            $this->output->error('Error: Provided filename does not appear to be valid');
        }

        // Direction (only relevant for --add)
        $direction = 'up';
        if ($this->arguments->getOpt('d')) {
            if ($this->arguments->getOpt('d') == 'up' || $this->arguments->getOpt('d') == 'down') {
                $direction = $this->arguments->getOpt('d');
            } else {
                $this->output->error('Error: Direction must be one of "up" or "down"');
            }
        }

        // Overriding default document root?
        $directory = null;
        if ($this->arguments->getOpt('r')) {
            if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r'))) {
                $directory = rtrim($this->arguments->getOpt('r'), DS);
            } else {
                $this->output->error('Error: Provided directory is not valid');
            }
        }

        // Migrating a super group
        $alternativeDatabase = null;
        if ($this->arguments->getOpt('group')) {
            $cname = $this->arguments->getOpt('group');
            $group = \Hubzero\User\Group::getInstance($cname);
            if ($group && $group->isSuperGroup()) {
                $groupsConfig = \Component::params('com_groups');
                $directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
                $directory .= DS . $group->get('gidNumber');

                if (!is_dir($directory . DS . 'migrations') || !is_readable($directory . DS . 'migrations')) {
                    $this->output->error('Error: Migrations directory does not exist.');
                }

                $alternativeDatabase = \Hubzero\User\Group\Helper::getDBO(array(), $group->get('cn'));

                if ($alternativeDatabase->getErrorNum() > 0) {
                    $this->output->error('Error: Could not connect to Group Database.');
                }
            } else {
                $this->output->error('Error: Provided group is not valid');
            }
        }

        // Specific extension filter
        $extension = null;
        if ($this->arguments->getOpt('e')) {
            $pattern = '/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|' .
                '^plg_[[:alnum:]]+_[[:alnum:]]+$|^tpl_[[:alnum:]]+$|^core$/i';
            if (!preg_match($pattern, $this->arguments->getOpt('e'))) {
                $this->output->error(
                    'Error: extension should match the pattern of ' .
                    'com_*, mod_*, tpl_*, plg_*_*, or core'
                );
            } else {
                $extension = $this->arguments->getOpt('e');
            }
        }

        // Dry run mode (default)
        $dryrun = !$this->arguments->getOpt('f');

        // Create migration object
        $migration = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

        if ($migration === false) {
            $this->output->error('Error: failed to instantiate new migration object.');
        }

        // Register callback for interactive output
        if ($this->output->isInteractive()) {
            $output = $this->output;
            $callback = function ($message, $type = null) use ($output) {
                $output->addLine($message, $type);
            };
            $migration->registerCallback('message', $callback);
        }

        // Get current status of the migration
        $currentStatus = $migration->getMigrationStatus($file, $extension);

        if ($add) {
            // Mark migration as executed
            if ($dryrun) {
                if ($currentStatus) {
                    $this->output->addLine(
                        "Would mark {$file} as {$direction} (currently: {$currentStatus->direction}, " .
                        "status: " . ($currentStatus->status ?? 'success') . ")",
                        'info'
                    );
                } else {
                    $this->output->addLine("Would mark {$file} as {$direction}", 'info');
                }
                $this->output->addLine('');
                $this->output->addLine('Dry run: use -f to actually mark the migration');
            } else {
                if ($migration->markMigration($file, $direction, $extension)) {
                    $this->output->addLine("Successfully marked {$file} as {$direction}", 'success');
                } else {
                    if (count($migration->get('log')) > 0) {
                        $this->output->addLinesFromArray($migration->get('log'));
                    }
                    $this->output->error('Failed to mark migration');
                }
            }
        } else {
            // Remove migration tracking record
            if ($dryrun) {
                if ($currentStatus) {
                    $this->output->addLine(
                        "Would remove tracking record for {$file} (currently: {$currentStatus->direction}, " .
                        "status: " . ($currentStatus->status ?? 'success') . ", date: {$currentStatus->date})",
                        'info'
                    );
                } else {
                    $this->output->addLine("No tracking record found for {$file}", 'warning');
                }
                $this->output->addLine('');
                $this->output->addLine('Dry run: use -f to actually remove the tracking record');
            } else {
                if ($migration->unmarkMigration($file, $extension)) {
                    $this->output->addLine("Successfully removed tracking record for {$file}", 'success');
                } else {
                    if (count($migration->get('log')) > 0) {
                        $this->output->addLinesFromArray($migration->get('log'));
                    }
                    $this->output->error('Failed to remove tracking record');
                }
            }
        }
    }

    /**
     * Refresh all migrations (rollback all, then re-run)
     *
     * This command is equivalent to running:
     *   muse migration run -d down --target=first -f
     *   muse migration run -f
     *
     * WARNING: This is a destructive operation that will rollback ALL migrations
     * and then re-run them. All data in affected tables may be lost.
     *
     * @museDescription  Rolls back all migrations then re-runs them (DESTRUCTIVE)
     *
     * @return  void
     **/
    public function refresh()
    {
        // Safety check: require explicit confirmation
        if (!$this->arguments->getOpt('reset-all-migrations')) {
            $this->output->addLine('');
            $this->output->addLine('WARNING: This command will rollback ALL migrations and re-run them.', 'error');
            $this->output->addLine('This is a DESTRUCTIVE operation that may result in data loss.', 'error');
            $this->output->addLine('');
            $this->output->addLine('To proceed, you must explicitly confirm by adding:', 'warning');
            $this->output->addLine('  --reset-all-migrations', 'info');
            $this->output->addLine('');
            $this->output->addLine('Example: muse migration refresh --reset-all-migrations -f');
            return;
        }

        // Dry run check
        $dryrun = !$this->arguments->getOpt('f');

        if ($dryrun) {
            $this->output->addLine('');
            $this->output->addLine('DRY RUN: No changes will be made.', 'warning');
            $this->output->addLine('');
        }

        // Overriding default document root?
        $directory = null;
        if ($this->arguments->getOpt('r')) {
            if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r'))) {
                $directory = rtrim($this->arguments->getOpt('r'), DS);
            } else {
                $this->output->error('Error: Provided directory is not valid');
            }
        }

        // Migrating a super group
        $alternativeDatabase = null;
        if ($this->arguments->getOpt('group')) {
            $cname = $this->arguments->getOpt('group');
            $group = \Hubzero\User\Group::getInstance($cname);
            if ($group && $group->isSuperGroup()) {
                $groupsConfig = \Component::params('com_groups');
                $directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
                $directory .= DS . $group->get('gidNumber');

                if (!is_dir($directory . DS . 'migrations') || !is_readable($directory . DS . 'migrations')) {
                    $this->output->error('Error: Migrations directory does not exist.');
                }

                $alternativeDatabase = \Hubzero\User\Group\Helper::getDBO(array(), $group->get('cn'));

                if ($alternativeDatabase->getErrorNum() > 0) {
                    $this->output->error('Error: Could not connect to Group Database.');
                }
            } else {
                $this->output->error('Error: Provided group is not valid');
            }
        }

        // Create migration object
        $migration = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

        if ($migration === false) {
            $this->output->error('Error: failed to instantiate new migration object.');
        }

        // Register callback for interactive output
        if ($this->output->isInteractive()) {
            $output = $this->output;
            $callback = function ($message, $type = null) use ($output) {
                $output->addLine($message, $type);
            };
            $migration->registerCallback('message', $callback);

            $progress = $this->output->getProgressOutput();
            $migration->registerCallback('progress', $progress);
        }

        $this->output->addLine('');
        $this->output->addLine('=== REFRESH: Rolling back all migrations ===', 'info');
        $this->output->addLine('');

        // Step 1: Find all migrations and run DOWN
        if ($migration->find() === false) {
            if (count($migration->get('log')) > 0) {
                $this->output->addLinesFromArray($migration->get('log'));
            }
            $this->output->error('Migration find failed! See log messages for details.');
        }

        // Resolve to first migration (rollback everything)
        $resolved = $migration->resolveAlias('first', null);
        if ($resolved === false || $resolved['file'] === null) {
            $this->output->addLine('No migrations to rollback.', 'info');
        } else {
            // Run all down migrations
            if (!$migration->migrate('down', false, $dryrun, true, false)) {
                if (count($migration->get('log')) > 0) {
                    $this->output->addLinesFromArray($migration->get('log'));
                }
                $this->output->error('Rollback failed! See log messages for details.');
            }
        }

        $this->output->addLine('');
        $this->output->addLine('=== REFRESH: Re-running all migrations ===', 'info');
        $this->output->addLine('');

        // Step 2: Find all migrations again and run UP
        $migration2 = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

        if ($this->output->isInteractive()) {
            $output = $this->output;
            $callback = function ($message, $type = null) use ($output) {
                $output->addLine($message, $type);
            };
            $migration2->registerCallback('message', $callback);

            $progress = $this->output->getProgressOutput();
            $migration2->registerCallback('progress', $progress);
        }

        if ($migration2->find() === false) {
            if (count($migration2->get('log')) > 0) {
                $this->output->addLinesFromArray($migration2->get('log'));
            }
            $this->output->error('Migration find failed! See log messages for details.');
        }

        if (!$migration2->migrate('up', false, $dryrun, true, false)) {
            if (count($migration2->get('log')) > 0) {
                $this->output->addLinesFromArray($migration2->get('log'));
            }
            $this->output->error('Migration up failed! See log messages for details.');
        }

        $this->output->addLine('');
        $this->output->addLine('Refresh complete!', 'success');
    }

    /**
     * Drop all tables and re-run all migrations from scratch
     *
     * This is equivalent to Laravel's migrate:fresh command.
     * It drops ALL tables (without running down() methods) and then
     * runs all migrations from scratch.
     *
     * WARNING: This is an EXTREMELY DESTRUCTIVE operation that will
     * permanently delete ALL data in ALL tables.
     *
     * @museDescription  Drops ALL tables and re-runs migrations (EXTREMELY DESTRUCTIVE)
     *
     * @return  void
     **/
    public function fresh()
    {
        // Safety check: require explicit confirmation
        if (!$this->arguments->getOpt('drop-all-tables')) {
            $this->output->addLine('');
            $this->output->addLine('!!! DANGER !!!', 'error');
            $this->output->addLine('');
            $this->output->addLine('This command will DROP ALL TABLES in your database.', 'error');
            $this->output->addLine('This is an EXTREMELY DESTRUCTIVE operation.', 'error');
            $this->output->addLine('ALL DATA WILL BE PERMANENTLY LOST.', 'error');
            $this->output->addLine('');
            $this->output->addLine('Unlike "refresh", this does NOT run down() migrations.', 'warning');
            $this->output->addLine('It simply deletes every table and starts fresh.', 'warning');
            $this->output->addLine('');
            $this->output->addLine('To proceed, you must explicitly confirm by adding:', 'warning');
            $this->output->addLine('  --drop-all-tables', 'info');
            $this->output->addLine('');
            $this->output->addLine('Example: muse migration fresh --drop-all-tables -f');
            return;
        }

        // Dry run check
        $dryrun = !$this->arguments->getOpt('f');

        if ($dryrun) {
            $this->output->addLine('');
            $this->output->addLine('DRY RUN: No changes will be made.', 'warning');
            $this->output->addLine('');
        }

        // Overriding default document root?
        $directory = null;
        if ($this->arguments->getOpt('r')) {
            if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r'))) {
                $directory = rtrim($this->arguments->getOpt('r'), DS);
            } else {
                $this->output->error('Error: Provided directory is not valid');
            }
        }

        // Migrating a super group
        $alternativeDatabase = null;
        if ($this->arguments->getOpt('group')) {
            $cname = $this->arguments->getOpt('group');
            $group = \Hubzero\User\Group::getInstance($cname);
            if ($group && $group->isSuperGroup()) {
                $groupsConfig = \Component::params('com_groups');
                $directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
                $directory .= DS . $group->get('gidNumber');

                if (!is_dir($directory . DS . 'migrations') || !is_readable($directory . DS . 'migrations')) {
                    $this->output->error('Error: Migrations directory does not exist.');
                }

                $alternativeDatabase = \Hubzero\User\Group\Helper::getDBO(array(), $group->get('cn'));

                if ($alternativeDatabase->getErrorNum() > 0) {
                    $this->output->error('Error: Could not connect to Group Database.');
                }
            } else {
                $this->output->error('Error: Provided group is not valid');
            }
        }

        // Get database connection
        $db = $alternativeDatabase ?? \App::get('db');

        if (!$db) {
            $this->output->error('Error: Could not get database connection.');
        }

        $this->output->addLine('');
        $this->output->addLine('=== FRESH: Dropping all tables ===', 'info');
        $this->output->addLine('');

        // Get list of all tables
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();

        if (empty($tables)) {
            $this->output->addLine('No tables found in database.', 'info');
        } else {
            // Filter to only tables with our prefix (if prefix is set)
            $tablesToDrop = [];
            foreach ($tables as $table) {
                if (empty($prefix) || strpos($table, $prefix) === 0) {
                    $tablesToDrop[] = $table;
                }
            }

            $this->output->addLine(sprintf('Found %d tables to drop.', count($tablesToDrop)));
            $this->output->addLine('');

            if (!$dryrun) {
                // Disable foreign key checks during drop
                try {
                    $db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
                    $db->execute();
                } catch (\Exception $e) {
                    // SQLite and some others don't support this, continue anyway
                }

                foreach ($tablesToDrop as $table) {
                    try {
                        $this->output->addLine(sprintf('  Dropping: %s', $table));
                        $db->dropTable($table, true);
                    } catch (\Exception $e) {
                        $this->output->addLine(sprintf('  Error dropping %s: %s', $table, $e->getMessage()), 'error');
                    }
                }

                // Re-enable foreign key checks
                try {
                    $db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
                    $db->execute();
                } catch (\Exception $e) {
                    // Ignore
                }

                $this->output->addLine('');
                $this->output->addLine(sprintf('Dropped %d tables.', count($tablesToDrop)), 'success');
            } else {
                foreach ($tablesToDrop as $table) {
                    $this->output->addLine(sprintf('  Would drop: %s', $table));
                }
                $this->output->addLine('');
                $this->output->addLine(sprintf('Would drop %d tables.', count($tablesToDrop)), 'info');
            }
        }

        $this->output->addLine('');
        $this->output->addLine('=== FRESH: Running all migrations ===', 'info');
        $this->output->addLine('');

        // Create migration object
        $migration = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

        if ($migration === false) {
            $this->output->error('Error: failed to instantiate new migration object.');
        }

        // Register callback for interactive output
        if ($this->output->isInteractive()) {
            $output = $this->output;
            $callback = function ($message, $type = null) use ($output) {
                $output->addLine($message, $type);
            };
            $migration->registerCallback('message', $callback);

            $progress = $this->output->getProgressOutput();
            $migration->registerCallback('progress', $progress);
        }

        // Find and run all migrations
        if ($migration->find() === false) {
            if (count($migration->get('log')) > 0) {
                $this->output->addLinesFromArray($migration->get('log'));
            }
            $this->output->error('Migration find failed! See log messages for details.');
        }

        if (!$migration->migrate('up', true, $dryrun, true, false)) {
            if (count($migration->get('log')) > 0) {
                $this->output->addLinesFromArray($migration->get('log'));
            }
            $this->output->error('Migration up failed! See log messages for details.');
        }

        $this->output->addLine('');
        $this->output->addLine('Fresh migration complete!', 'success');
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this
            ->output
            ->addOverview(
                'Run a migration. This includes searching for migration files,
				depending on the options provided.'
            )
            ->addTasks($this)
            ->addArgument(
                '-d: direction [up|down]',
                'If not specified, defaults to "up".',
                'Example: -d=up or -d=down'
            )
            ->addArgument(
                '-r: document root',
                'Specify the document root through which the the application
				will search for migrations directories. The primary use case
				for this is specifying an alternate directory for testing.
				By default, it will use the PATH_CORE constant for
				the document root.',
                'Example: -r=/www/myhub/unittests/migrations'
            )
            ->addArgument(
                '-e: extension',
                'Explicity give the extension on which the migration should be run.
				This could be one of "com_componentname", "mod_modulename", "tpl_templatename",
				or "plg_plugingroup_pluginname". This option is required
				when using the force (--force) option and the log only option (-m).',
                'Example: -e=com_courses, -e=plg_members_dashboard'
            )
            ->addArgument(
                '-a: list all',
                'List all will display all migrations found, not just those needing
				to be run. This allows you to see the files that need to be run in the
				context of the other files that have already been run. This differs from
				the prior -i argument which was needed because, by default, only new
				files were considered for a run. Now, all files needing to be run are
				included by default, irrespective of whether or not they are dated after
				the last run migration.'
            )
            ->addArgument(
                '-i: ignore dates',
                'DEPRECATED: Now functions as if the -a option were given.
				Using this option will scan for and run all migrations that haven\'t
				previously been run, irrespective of the date of the migration.
				This differs from the default behavior in that normally, only files
				dated after the last run date will be eligable to be included in the
				migration. This option also differs from force mode (--force) in that it
				will find all migrations, but only run those that haven\'t been run
				before (whereas --force will run them irrespective of whether or not it
				thinks they\'ve already been run). You do not have to use -e with this
				option. This option is necessary when needing to run migrations that
				have been skipped for one reason or another.'
            )
            ->addArgument(
                '-f: full run',
                'By default, using the migration command without any options will run
				in dry-run mode (meaning no changes will actually be made), displaying
				the migrations that would be run, were the command to be fully executed.
				Use the "-f" (full run) option to do the full migration run.'
            )
            ->addArgument(
                '-m: log only',
                'Using this option, a migration will run as normal, and log entries
				will be created, but the SQL itself will not be run. As a general
				precaution, this should not be run without the extension option (-e).
				The primary use case for this option would be marking a migration
				as run in the event that it had already been run (manually), yet
				not logged in the database.'
            )
            ->addArgument(
                '--file: run a provided filed',
                'Provide the filename to be run. This and only this file will be run.
				This will automatically place the migration in (-i) mode, ignoring dates.
				It will not, however, force it to be run, if a log entry for this file
				and direction already exists. Use the (--force) option to override this
				behavior or run the opposite direction first.',
                'Example: --file=Migration20130101000000ComMigrations.php'
            )
            ->addArgument(
                '--force: force mode',
                'This option should be used carefully. It will run a migration,
				even if it thinks it has already been run. When using this option,
				you must also give a specific extension using the (-e) option.'
            )
            ->addArgument(
                '--target: version alias',
                'Specify a migration target using an alias instead of a filename.
				Valid aliases: first, prev, current, next, latest.
				- first: The oldest migration file
				- prev: The migration before the last executed one
				- current: The last successfully executed migration
				- next: The next pending migration
				- latest: The newest migration file',
                'Example: --target=next, --target=prev, --target=latest'
            )
            ->addArgument(
                '--email: send email',
                'Specify an email address to receive the output of this run. If no
				files are executed during the migration, an email will not be sent.',
                'Example: --email=sampleuser@hubzero.org'
            )
            ->addArgument(
                '-v: verbose (status command)',
                'When used with the status command, shows detailed lists of pending,
				failed, and recent migrations instead of just summary counts.'
            )
            ->addArgument(
                '--group: super group',
                'Run migrations for a specific super group. This will search for
				migrations in the group\'s directory and run against the group\'s database.',
                'Example: --group=mygroup'
            )
            ->addArgument(
                '--vendor: include vendor',
                'Include vendor directory in migration search paths. Useful for
				third-party packages that include their own migrations.'
            )
            ->addArgument(
                '--all-or-nothing: batch transaction mode',
                'Wraps all migrations in a single transaction. If any migration fails,
				the entire batch is rolled back to the original state. This ensures
				atomicity across multiple migrations.

				WARNING: MySQL DDL statements (CREATE TABLE, ALTER TABLE, DROP TABLE)
				cause implicit commits and cannot be rolled back. This mode is most
				useful for data-only migrations or with databases that support
				transactional DDL (PostgreSQL, SQLite).',
                'Example: muse migration run -f --all-or-nothing'
            )
            ->addSpacer()
            ->addSection('Mark Command')
            ->addParagraph(
                'The mark command allows you to add or remove migration tracking records
				without actually executing the migration. This is useful for:
				- Fixing tracking table mismatches after manual database changes
				- Recovering from partial failures
				- Syncing tracking state with external changes'
            )
            ->addArgument(
                '--add',
                'Mark a migration as executed without running it. Creates a tracking
				record as if the migration had been run successfully.
				Use with --file to specify which migration to mark.
				Use with -d to specify direction (defaults to "up").',
                'Example: muse migration mark --add --file=Migration20230101000000Core.php -f'
            )
            ->addArgument(
                '--delete',
                'Remove the most recent tracking record for a migration. This makes the
				migration appear as "pending" again, allowing it to be re-run.
				Use with --file to specify which migration to unmark.',
                'Example: muse migration mark --delete --file=Migration20230101000000Core.php -f'
            )
            ->addParagraph(
                'Note: Like other migration commands, mark runs in dry-run mode by default.
				Use -f to actually make changes to the tracking table.'
            )
            ->addSpacer()
            ->addSection('Refresh Command')
            ->addParagraph(
                'The refresh command rolls back ALL migrations and then re-runs them.
				This is useful for development and testing when you want to reset your
				database to a known state while preserving migration tracking.

				WARNING: This is a DESTRUCTIVE operation that may result in data loss.
				All data in affected tables will be lost when down() migrations run.'
            )
            ->addArgument(
                '--reset-all-migrations',
                'REQUIRED safety flag. You must include this flag to confirm that you
				understand the refresh command will rollback and re-run all migrations.',
                'Example: muse migration refresh --reset-all-migrations -f'
            )
            ->addSpacer()
            ->addSection('Fresh Command')
            ->addParagraph(
                'The fresh command drops ALL tables and then runs all migrations from scratch.
				Unlike refresh, it does NOT run down() migrations - it simply drops every
				table directly. This is faster but more destructive.

				!!! DANGER !!! This is an EXTREMELY DESTRUCTIVE operation.
				ALL DATA IN ALL TABLES WILL BE PERMANENTLY LOST.'
            )
            ->addArgument(
                '--drop-all-tables',
                'REQUIRED safety flag. You must include this flag to confirm that you
				understand the fresh command will drop ALL tables without running down()
				migrations. This cannot be undone.',
                'Example: muse migration fresh --drop-all-tables -f'
            );
    }
}
