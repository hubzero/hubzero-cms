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
		if ($this->arguments->getOpt('d'))
		{
			if ($this->arguments->getOpt('d') == 'up' || $this->arguments->getOpt('d') == 'down')
			{
				$direction = $this->arguments->getOpt('d');
			}
			else
			{
				$this->output->error('Error: Direction must be one of "up" or "down"');
			}
		}

		// Overriding default document root?
		$directory = null;
		if ($this->arguments->getOpt('r'))
		{
			if (is_dir($this->arguments->getOpt('r')) && is_readable($this->arguments->getOpt('r')))
			{
				$directory = rtrim($this->arguments->getOpt('r'), DS);
			}
			else
			{
				$this->output->error('Error: Provided directory is not valid');
			}
		}

		// Migrating a super group
		$alternativeDatabase = null;
		if ($this->arguments->getOpt('group'))
		{
			$cname = $this->arguments->getOpt('group');
			$group = \Hubzero\User\Group::getInstance($cname);
			if ($group && $group->isSuperGroup())
			{
				// Get group config
				$groupsConfig = \Component::params('com_groups');

				// Path to group folder
				$directory  = PATH_APP . DS . trim($groupsConfig->get('uploadpath', '/site/groups'), DS);
				$directory .= DS . $group->get('gidNumber');

				// make sure we have migrations dir
				if (!is_dir($directory . DS . 'migrations') || !is_readable($directory . DS . 'migrations'))
				{
					$this->output->error('Error: Migrations directory does not exist.');
				}

				// Get group database
				$alternativeDatabase = \Hubzero\User\Group\Helper::getDBO(array(), $group->get('cn'));

				// make sure we have a group db
				if ($alternativeDatabase->getErrorNum() > 0)
				{
					$this->output->error('Error: Could not connect to Group Database.');
				}
			}
			else
			{
				$this->output->error('Error: Provided group is not valid');
			}
		}

		// Forcing update
		$force = false;
		if ($this->arguments->getOpt('force'))
		{
			if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file'))
			{
				$this->output->error('Error: You cannot specify the "force" option without specifying a specific extention or file');
			}
			else
			{
				$force = true;
			}
		}

		// Logging only - record migration
		$logOnly = false;
		if ($this->arguments->getOpt('m'))
		{
			if (!$this->arguments->getOpt('e') && !$this->arguments->getOpt('file'))
			{
				$this->output->error('Error: You cannot specify the "Log only (-m)" option without specifying a specific extention or file');
			}
			else
			{
				$logOnly = true;
			}
		}

		// Ignore dates
		$listAll = false;
		if ($this->arguments->getOpt('a') || $this->arguments->getOpt('i'))
		{
			$listAll = true;
		}

		// Specific extension
		$extension = null;
		if ($this->arguments->getOpt('e'))
		{
			if (!preg_match('/^com_[[:alnum:]]+$|^mod_[[:alnum:]]+$|^plg_[[:alnum:]]+_[[:alnum:]]+$|^core$/i', $this->arguments->getOpt('e')))
			{
				$this->output->error('Error: extension should match the pattern of com_*, mod_*, plg_*_*, or core');
			}
			else
			{
				$extension = $this->arguments->getOpt('e');
			}
		}

		// Specific file
		$file = null;
		if ($this->arguments->getOpt('file'))
		{
			if (!preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $this->arguments->getOpt('file')))
			{
				$this->output->error('Error: Provided filename does not appear to be valid');
			}
			else
			{
				$file = $this->arguments->getOpt('file');

				// Also force "ignore dates mode", as that's somewhat implied by giving a specific filename
				$listAll = true;
			}
		}

		// Dryrun
		$dryrun = true;
		if ($this->arguments->getOpt('f'))
		{
			$dryrun = false;
		}

		// Email results
		$email = false;
		if ($this->arguments->getOpt('email'))
		{
			if (!preg_match('/^[a-zA-Z0-9\.\_\-]+@[a-zA-Z0-9\.]+\.[a-zA-Z]{2,4}$/', $this->arguments->getOpt('email')))
			{
				$this->output->error('Error: ' . $this->arguments->getOpt('email') . ' does not appear to be a valid email address');
			}
			else
			{
				$email = $this->arguments->getOpt('email');
			}
		}

		// Create migration object
		$migration = new \Hubzero\Content\Migration($directory, $alternativeDatabase);

		// Search vendor directories?
		if ($this->arguments->getOpt('vendor'))
		{
			$vendorPath = PATH_APP . DS . 'vendor';

			if (is_dir($vendorPath))
			{
				foreach (scandir($vendorPath) as $namespace)
				{
					if ($namespace != '.' && $namespace != '..' && is_dir($vendorPath . DS . $namespace))
					{
						foreach (scandir($vendorPath . DS . $namespace) as $package)
						{
							if ($package != '.' && $package != '..' && is_dir($vendorPath . DS . $namespace . DS . $package))
							{
								$migrationPath = $vendorPath . DS . $namespace . DS . $package . DS . 'src';
								if (is_dir($migrationPath . DS . 'migrations'))
								{
									$migration->addSearchPath($migrationPath);
								}
							}
						}
					}
				}
			}
		}

		// Make sure we got a migration object
		if ($migration === false)
		{
			$this->output->error('Error: failed to instantiate new migration object.');
		}

		if ($this->output->isInteractive())
		{
			// Register callback function for adding lines interactively
			$output   = $this->output;
			$callback = function($message, $type=null) use ($output)
			{
				$output->addLine($message, $type);
			};
			$migration->registerCallback('message', $callback);

			// Add progress callback as well
			$progress = $this->output->getProgressOutput();
			$migration->registerCallback('progress', $progress);
		}

		// Find migration files
		if ($migration->find($extension, $file) === false)
		{
			// Find failed, do nothing
			if (count($migration->get('log')) > 0)
			{
				$this->output->addLinesFromArray($migration->get('log'));
			}
			$this->output->error('Migration find failed! See log messages for details.');
		}
		else // no errors during 'find', so continue
		{
			// Run migration itself
			if (!$result = $migration->migrate($direction, $force, $dryrun, $listAll, $logOnly))
			{
				if (count($migration->get('log')) > 0)
				{
					$this->output->addLinesFromArray($migration->get('log'));
				}
				$this->output->error('Migration failed! See log messages for details.');
			}
			else
			{
				if (!$this->output->isInteractive())
				{
					if ($this->output->getMode() == 'minimal')
					{
						if (count($migration->get('log')) > 0)
						{
							$missed   = array();
							$pending  = array();
							$complete = array();
							foreach ($migration->get('log') as $log)
							{
								if (preg_match('/would run up\(\) (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i', $log['message'], $matches))
								{
									$pending[] = $matches[1] . $matches[2];
								}
								if (preg_match('/completed up\(\) in (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i', $log['message'], $matches)
								 || preg_match('/would ignore up\(\) (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php)/i', $log['message'], $matches))
								{
									$complete[] = $matches[1] . $matches[2];
								}
								if (preg_match('/migration up\(\) in (.*?)(Migration[0-9]{14}[[:alnum:]_]*\.php) has not been run/i', $log['message'], $matches))
								{
									$missed[] = $matches[1] . $matches[2];
								}
							}

							if (count($pending) > 0)
							{
								$this->output->addLine(array('pending'  => $pending));
							}
							if (count($missed) > 0)
							{
								$this->output->addLine(array('missed'   => $missed));
							}
							if (count($complete) > 0)
							{
								$this->output->addLine(array('complete' => $complete));
							}
						}
					}
					else
					{
						$this->output->addLinesFromArray($migration->get('log'));
					}
				}

				// Final success message
				if ($this->output->getMode() != 'minimal')
				{
					$this->output->addLine('Success: ' . ucfirst($direction) . ' migration complete!', 'success');
				}
			}
		}

		// Email results if requested (only do so if there's something to report)
		if ($email && count($migration->get('affectedFiles')) > 0)
		{
			$this->output->addLine("Emailing results to: {$email}");

			$headers = "From: Migrations <automator@" . php_uname("n") . ">";
			$subject = "Migration output - " . php_uname("n") . " [" . date("d-M-Y H:i:s") . "]";

			$message = "";
			foreach ($migration->get('log') as $line)
			{
				$message .= $line['message'] . "\n";
			}

			// Send the message
			if (!mail($email, $subject, $message, $headers))
			{
				$this->output->addLine("Error: failed to send message!", 'warning');
			}
		}
		elseif ($email)
		{
			$this->output->addLine('Ignoring email as no files were affected in this run.', 'info');
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
		$maxFile   = 0;
		$maxUser   = 0;
		$maxScope  = 0;


		if ($history && count($history) > 0)
		{
			$items[] = [
				'File',
				'By',
				'Direction',
				'Date'
			];

			foreach ($history as $entry)
			{
				$items[] = [
					$entry->scope . DS . $entry->file,
					$entry->action_by,
					$entry->direction,
					$entry->date
				];
			}

			$this->output->addTable($items, true);
		}
		else
		{
			$this->addLine('No history to display.');
		}
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
				This could be one of "com_componentname", "mod_modulename",
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
				'--email: send email',
				'Specify an email address to receive the output of this run. If no
				files are executed during the migration, an email will not be sent.',
				'Example: --email=sampleuser@hubzero.org'
			);
	}
}
