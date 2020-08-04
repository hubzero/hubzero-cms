<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;
use Hubzero\Utility\Date;
use App;

/**
 * Scaffolding class for migrations
 *
 * @museIgnoreHelp
 **/
class Migration extends Scaffolding
{
	/**
	 * Construct new migration script
	 *
	 * @return  void
	 **/
	public function construct()
	{
		switch ($this->arguments->getOpt(4))
		{
			case 'for':
				$prefix = App::get('db')->getPrefix();
				$tables = App::get('db')->getTableList();

				if (!$table = $this->arguments->getOpt(5))
				{
					$this->output->error('Please specify the table for which a migration is being created');
				}
				else if (!in_array($table, $tables))
				{
					$this->output->error('Table does not exist');
				}

				$this->addReplacement('description', "creating table {$table}")
					 ->addReplacement('table_name', str_replace($prefix, '#__', $table))
					 ->addReplacement('up', '$^create.table^$')
					 ->addReplacement('down', '$^drop.table^$')
					 ->addReplacement('create_table', $this->showCreateTable($table));
				break;

			default:
				$this->addReplacement('description', '...')
					 ->addReplacement('up', '')
					 ->addReplacement('down', '');
				break;
		}

		// Determine our base path
		$base       = $this->arguments->getOpt('app') ? PATH_APP : PATH_CORE;
		$installDir = trim($this->arguments->getOpt('install-dir'));
		if ($installDir && strlen($installDir) > 0)
		{
			if (substr($installDir, 0, 1) == DS)
			{
				$base = rtrim($installDir, DS);
			}
			else
			{
				$base .= DS . trim($installDir, DS);
			}
		}

		// Install directory is migrations folder within base
		$installDir = $base . DS . 'migrations';

		// Extension
		$extension = null;
		if ($this->arguments->getOpt('e') || $this->arguments->getOpt('extension'))
		{
			$extension = ($this->arguments->getOpt('e')) ? $this->arguments->getOpt('e') : $this->arguments->getOpt('extension');

			if ($extension != 'core' && !$this->isValidExtension($extension, $base) && !$this->arguments->getOpt('i'))
			{
				$this->output->error("Error: the extension provided ({$extension}) does not appear to be valid.");
			}
		}
		else
		{
			$this->output->error("Error: an extension should be provided.");
		}

		// Editor
		$editor = null;
		if ($this->arguments->getOpt('editor'))
		{
			$editor = $this->arguments->getOpt('editor');
		}
		else
		{
			$editor = (getenv('EDITOR')) ? getenv('EDITOR') : 'vi';
		}

		// Create filename varient of extension
		$ext = '';
		if (!preg_match('/core/i', $extension))
		{
			$parts = explode('_', $extension);
			foreach ($parts as $part)
			{
				$ext .= ucfirst($part);
			}
		}
		else
		{
			$ext = 'Core';
		}

		// Craft file/classname
		$classname   = 'Migration' . with(new Date('now'))->format("YmdHis") . $ext;
		$destination = $installDir . DS . $classname . '.php';

		$this->addTemplateFile("{$this->getType()}.tmpl", $destination)
			 ->addReplacement('class_name', $classname)
			 ->make();

		// Open in editor
		system("{$editor} {$destination} > `tty`");
	}

	/**
	 * Simple helper function to check validity of provided extension name
	 *
	 * @param   string  $extension  Extension name to evaluate
	 * @param   string  $base       Directory to examine
	 * @return  bool
	 **/
	private function isValidExtension($extension, $base)
	{
		$ext = explode('_', $extension);
		$dir = '';

		switch ($ext[0])
		{
			case 'com':
				$dir = $base . DS . 'components' . DS . $extension;
			break;
			case 'mod':
				$dir = $base . DS . 'modules' . DS . $extension;
			break;
			case 'plg':
				$dir = $base . DS . 'plugins' . DS . $ext[1] . DS . $ext[2];
			break;
			default:
				return false;
			break;
		}

		return (is_dir($dir)) ? true : false;
	}

	/**
	 * Get table creation string
	 *
	 * @param   string  $tableName  The table name for which to retrieve create syntax
	 * @return  string
	 **/
	private function showCreateTable($tableName)
	{
		$prefix = App::get('db')->getPrefix();

		$create = App::get('db')->getTableCreate($tableName);
		$create = $create[$tableName];
		$create = str_replace("CREATE TABLE `{$prefix}", 'CREATE TABLE `#__', $create);
		$create = str_replace("\n", "\n\t\t\t\t", $create);
		$create = preg_replace('/(AUTO_INCREMENT=)([0-9]*)/', '${1}0', $create);

		return $create;
	}

	/**
	 * Help doc for migration scaffolding class
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->addOverview(
				'Create a migration script from the default template. An
				extension must be provided.'
			)
			->addArgument(
				'-e, --extension: extension',
				'Specify the extension for which you are creating a migration
				script. Those scripts not pertaining to a specific extension
				should be given the extension "core"',
				'Example: -e=com_courses, --extension=plg_members_dashboard',
				true
			)
			->addArgument(
				'-i: ignore validity check',
				'Normally, migrations scaffolding tries to check the validity of the provided
				extension name by checking for the existance of a corresponding
				directory within the framework. Occasionally, migrations need to be
				written for non-existent extensions. This option will override the
				validity check and allow you to create the migration anyways.',
				'Example: -i'
			)
			->addArgument(
				'--install-dir: installation directory',
				'Installation/base directory within which the migration will be installed.
				By default, this will be PATH_CORE. The command will then look for a 
				directory named "migrations" within the provided installation directory.',
				'Example: --install-dir=/www/myhub'
			)
			->addArgument(
				'--app: use app as the base path, rather than core',
				'Use the app, rather than the core directory.  This will effect both the
				question of whether or not the provided extension appears to be valid,
				as well as where the migration will be saved.',
				'Example: --app'
			)
			->addArgument(
				'--editor: editor',
				'Specify the editor to use when creating the migration file.
				You\'ll be dropped into this editor after scaffolding pre-populates
				everything it can',
				'Example: --editor=nano'
			)
			->addSection(
				'Migration methods (available within a migration)'
			)
			->addParagraph(
				'Migrations have several common methods available to the creator of the migration.
				These are listed below. The methods below that are intended to
				display output should be passed through the callback() function
				so that it can make sure the migration is running in interactive
				mode and everything is properly set up. The examples indicate
				proper use of each method.',
				array(
					'indentation' => 2
				)
			)
			->addSpacer()
			->addArgument(
				'addComponentEntry($name, $option=NULL, $enabled=1, $params=\'\', $createMenuItem=true)',
				'Adds a new component entry to the database, creating it only if
				needed. Params should be JSON encoded.',
				'Example: $this->addComponentEntry(\'com_awesome\');'
			)
			->addArgument(
				'addPluginEntry($folder, $element, $enabled=1, $params=\'\')',
				'Adds a new plugin entry to the database, creating it only if
				needed. Params should be JSON encoded.',
				'Example: $this->addPluginEntry(\'groups\', \'members\');'
			)
			->addArgument(
				'addModuleEntry($element, $enabled=1, $params=\'\')',
				'Adds a new module entry to the database, creating it only if
				needed. Params should be JSON encoded.',
				'Example: $this->addModuleEntry(\'mod_awesome\');'
			)
			->addArgument(
				'deleteComponentEntry($name)',
				'Removes a component entry by name from the database.',
				'Example: $this->deleteComponentEntry(\'com_awesome\');'
			)
			->addArgument(
				'deletePluginEntry($folder, $element=NULL)',
				'Removes a plugin entry by name from the database. Leaving the element
				argument empty will delete all plugins for the specified folder.',
				'Example: $this->deleteComponentEntry(\'groups\', \'members\');'
			)
			->addArgument(
				'deleteModuleEntry($element)',
				'Removes a module entry by name from the database.',
				'Example: $this->deleteModuleEntry(\'mod_awesome\');'
			)
			->addArgument(
				'enablePlugin($folder, $element)',
				'Enables (turns on) a plugin.',
				'Example: $this->enablePlugin(\'groups\', \'members\');'
			)
			->addArgument(
				'disablePlugin($folder, $element)',
				'Disables (turns off) a plugin.',
				'Example: $this->disablePlugin(\'groups\', \'members\');'
			)
			->addArgument(
				'progress:init',
				'Initialize a progress tracker. Can provide one argument to the
				method giving a message that will be displayed before the
				percentage counter.',
				'Example: $this->callback(\'progress\', \'init\', array(\'Running \' . __CLASS__ . \'.php:\'));'
			)
			->addArgument(
				'progress:setProgress',
				'Update the current progress value. Should provide one argument
				specifying the current progress value [(int) 1 - 100].',
				'Example: $this->callback(\'progress\', \'setProgress\', array($i));'
			)
			->addArgument(
				'progress:done',
				'Terminate the progress tracker. This will back the cursor up
				to the beginning of the line so future text can overwrite it.
				In the case of migrations, this will likely mean that the line
				indicating successful completion of the file will be shown.
				No arguments are expected.',
				'Example: $this->callback(\'progress\', \'done\');'
			);
	}
}
