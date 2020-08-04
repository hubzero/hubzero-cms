<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;
use Hubzero\Utility\Date;

/**
 * Scaffolding class for migrations
 *
 * @museIgnoreHelp
 **/
class SearchPlugin extends Scaffolding
{
	/**
	 * Construct new migration script
	 *
	 * @return  void
	 **/
	public function construct()
	{
		if ($this->arguments->getOpt(4))
		{
				$extension = $this->arguments->getOpt(4);
				$this->addReplacement('plugin_name', $extension);
				$this->addReplacement('extension', $extension);
		}
		else
		{
			$this->output->error("Error: must specify a hubtype.");
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
		$installDir = $base . DS . 'plugins' . DS . 'search';

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

		// @TODO detect previous file, warn about override

		// Make plugin file
		$classname   = 'plgSearch' . ucfirst($extension);
		$destination = $installDir . DS . $extension . DS .  $extension. '.php';

		// Make directory
		if (!is_dir($installDir. DS . $extension))
		{
			App::get('filesystem')->makeDirectory($installDir . DS . $extension);
		}

		$this->addTemplateFile("{$this->getType()}.tmpl", $destination)
			 ->addReplacement('class_name', $classname)
			 ->make();
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
