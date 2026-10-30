<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Utility\Date;
use Hubzero\Config\Registry;
use Hubzero\Content\Migration\Base as Migration;
use Config;

/**
 * Database class
 **/
class Database extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
	}

	/**
	 * Dump the database
	 *
	 * @museDescription  Dumps the hub's database into a file
	 *
	 * Give the file to write as the argument, or leave it out to write one
	 * in the home directory. Tables without the hub's prefix are left out
	 * unless --all-tables is given. The dump is consistent without locking
	 * the hub, and only its owner can read it.
	 *
	 * @return  void
	 **/
	public function dump()
	{
		$db       = \App::get('db');
		$tables   = $db->getTableList();
		$prefix   = $db->getPrefix();
		$excludes = [];
		$includes = (array)$this->arguments->getOpt('include-table', []);

		if (!$this->arguments->getOpt('all-tables'))
		{
			$this->output->addLine('Dumping database with all prefixed tables included');
			foreach ($tables as $table)
			{
				if (strpos($table, $prefix) !== 0 && !in_array(str_replace('#__', $prefix, $table), $includes))
				{
					$excludes[] = '--ignore-table=' . escapeshellarg(Config::get('db') . '.' . $table);
				}
				elseif (in_array(str_replace('#__', $prefix, $table), $includes))
				{
					$this->output->addLine('Also including `' . $table . '`');
				}
			}
		}
		else
		{
			$this->output->addLine('Dumping database with all tables included');
		}

		if (!$filename = $this->file())
		{
			$now      = new Date;
			$filename = tempnam(getenv('HOME'), gethostname() . '.mysql.dump.' . $now->format('Y.m.d') . '.sql.');
		}

		// Made first, so that the dump is never readable by anyone else
		if (!touch($filename) || !chmod($filename, 0600))
		{
			$this->output->error('Cannot write ' . $filename);
		}

		$client      = $this->client(['mariadb-dump', 'mysqldump']);
		$credentials = $this->credentials();

		$cmd = escapeshellarg($client)
			. ' --defaults-extra-file=' . escapeshellarg($credentials)
			. ' --single-transaction --no-tablespaces --routines --triggers '
			. implode(' ', $excludes) . ' '
			. escapeshellarg(Config::get('db'))
			. ' > ' . escapeshellarg($filename);

		exec($cmd, $output, $status);
		unlink($credentials);

		if ($status !== 0)
		{
			$this->output->error('The dump failed; ' . $filename . ' is incomplete.');
		}

		$this->output->addLine('File saved to: ' . $filename, 'success');
	}

	/**
	 * Load a database dump
	 *
	 * @museDescription  Loads the provided database into the hubs currently configured database
	 *
	 * Give the dump to load as the argument, or "-" to read it from standard
	 * input, as from a file the hub's account cannot read itself.
	 *
	 * By default the settings of a few extensions that differ between hubs,
	 * such as the paths and services of a development hub, are kept across
	 * the load. Pass --exact to restore a hub's own dump just as it was, and
	 * --drop-all-tables to remove tables the dump does not mention.
	 *
	 * @return  void
	 **/
	public function load()
	{
		if (!$infile = $this->file())
		{
			$this->output->error('Please provide an input file');
		}
		elseif ($infile !== '-' && !is_file($infile))
		{
			$this->output->error("'{$infile}' does not appear to be a valid file");
		}

		$exact = (bool) $this->arguments->getOpt('exact');
		$db    = \App::get('db');

		// First, set some things aside that we need to reapply after the update
		$params = [];
		if (!$exact)
		{
			$params['com_system']             = \Component::params('com_system');
			$params['com_tools']              = \Component::params('com_tools');
			$params['com_usage']              = \Component::params('com_usage');
			$params['com_members']            = \Component::params('com_members');
			$params['plg_projects_databases'] = \Plugin::params('projects', 'databases');
		}

		// See if we should drop all tables first
		if ($this->arguments->getOpt('drop-all-tables'))
		{
			$this->output->addLine('Dropping all tables...');

			$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
			$db->query();

			foreach ($db->getTableList() as $table)
			{
				$db->dropTable($table);
			}

			$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
			$db->query();
		}

		$client      = $this->client(['mariadb', 'mysql']);
		$credentials = $this->credentials();

		// A file of "-" is standard input, which the client inherits
		$cmd = escapeshellarg($client)
			. ' --defaults-extra-file=' . escapeshellarg($credentials)
			. ' ' . escapeshellarg(Config::get('db'))
			. ($infile === '-' ? '' : ' < ' . escapeshellarg($infile));

		$this->output->addLine('Loading data from ' . ($infile === '-' ? 'standard input' : $infile) . '...');

		// Now push the big red button
		exec($cmd, $output, $status);
		unlink($credentials);

		if ($status !== 0)
		{
			$this->output->error('The load failed.');
		}

		$migration = new Migration($db);

		// Now load some things back up
		foreach ($params as $k => $v)
		{
			if (!empty($v))
			{
				$migration->saveParams($k, $v);
			}
		}

		$this->output->addLine('Load complete!', 'success');
	}

	/**
	 * The file named on the command line, before or after its options, or
	 * "-" for standard input
	 *
	 * @return  string|null
	 **/
	private function file()
	{
		$opts = (array) $this->arguments->getOpts();

		foreach ($opts as $key => $value)
		{
			if (is_int($key) && $key >= 3)
			{
				return $value;
			}
		}

		// A lone "-" reads as an option with no name
		return array_key_exists('', $opts) ? '-' : null;
	}

	/**
	 * Find the first of the named database clients that is installed
	 *
	 * MariaDB's own names first; servers before 10.5 have only MySQL's.
	 *
	 * @param   array   $names
	 * @return  string
	 **/
	private function client(array $names)
	{
		foreach ($names as $name)
		{
			$path = trim((string) shell_exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null'));

			if ($path !== '')
			{
				return $path;
			}
		}

		$this->output->error('Neither ' . implode(' nor ', $names) . ' is installed. Install the MariaDB client.');
	}

	/**
	 * Write the hub's database credentials where only this account can read them
	 *
	 * The clients read them from the file, so the password is never on a
	 * command line, where anyone on the server could see it.
	 *
	 * @return  string  The file's path; remove it when done
	 **/
	private function credentials()
	{
		$file = tempnam(sys_get_temp_dir(), 'muse-db-');
		chmod($file, 0600);

		$options = [
			'user'     => Config::get('user'),
			'password' => Config::get('password'),
		];

		if (Config::get('socket'))
		{
			$options['socket'] = Config::get('socket');
		}
		else
		{
			$options['host'] = Config::get('host', 'localhost');

			if (Config::get('port'))
			{
				$options['port'] = Config::get('port');
			}
		}

		$content = "[client]\n";
		foreach ($options as $key => $value)
		{
			$content .= $key . '="' . addcslashes((string) $value, "\\\"") . "\"\n";
		}

		file_put_contents($file, $content);

		return $file;
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
				'Database utility functions for migrating and restoring database backups.
				The necessity for this function arose primarily from the need to copy
				a production database down to development environments without overwriting
				certain development configuration with inappropriate production values.'
			)
			->addTasks($this)
			->addArgument(
				'--include-table: Include a specific table',
				'Specify a given table to be included in the dump. This primarily
				would be used to include a given table from the non-prefixed namespace.',
				'Example: --include-table=migration'
			)
			->addArgument(
				'--all-tables: Include all tables',
				'By default, the database dump does not include non-prefixed tables
				(example: host, display, etc...). This option can be used to include
				these tables. Use with caution when planning to eventually load this
				data into another host (ex: dev) as it rarely makes sense to reload
				tool sessions into another environment.'
			)
			->addArgument(
				'--exact: Load the dump just as it was',
				'When loading in a database dump, keep none of the current settings
				that a load otherwise carries over. Use it to restore a hub from its
				own backup.'
			)
			->addArgument(
				'--drop-all-tables: Drop all tables',
				'When loading in a database dump, this option will drop all tables
				prior to loading in the given dump. This is often helpful when the
				applied dump is divergent in schema from the current database being
				overwritten.'
			);
	}
}
