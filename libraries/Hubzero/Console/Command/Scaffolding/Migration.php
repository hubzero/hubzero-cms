<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Scaffolding class for migrations
 **/
class Migration extends Scaffolding
{
	/**
	 * Construct new migration script
	 *
	 * @return void
	 **/
	public function construct()
	{
		// Extension
		$extension = null;
		if ($this->arguments->getOpt('e') || $this->arguments->getOpt('extension'))
		{
			$extension = ($this->arguments->getOpt('e')) ? $this->arguments->getOpt('e') : $this->arguments->getOpt('extension');

			if ($extension != 'core' && !$this->isValidExtension($extension))
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
		$classname   = 'Migration' . \JFactory::getDate()->format("YmdHis") . $ext;
		$destination = JPATH_ROOT . DS . 'migrations' . DS . $classname . '.php';

		$this->getQueryType()
			 ->addTemplateFile("{$this->getType()}.tmpl", $destination)
			 ->addReplacement('class_name', $classname)
			 ->make();

		// Open in editor
		system("{$editor} {$destination} > `tty`");

		$this->output->addLine("New migration script '{$destination}' successfully created!", 'success');
	}

	/**
	 * Help doc for migration scaffolding class
	 *
	 * @return void
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
				'--table: specify the table name',
				'Specify what table the migration should apply to. If the table
				exists, the migration will create an alter statement, otherwise
				it will create the table.',
				'Example: --table=jos_courses_assets'
			)
			->addArgument(
				'--fields: specify the fields',
				'Specify the fields that should be involved in the migration.
				Again, if the table already exists, the fields involved will
				be used in the alter statement. Otherwise, the fields will
				compose the newly created table.',
				'Example: --fields="id=>int(11)=>NOT NULL=>AUTO_INCREMENT"'
			)
			->addArgument(
				'--editor: editor',
				'Speicify the editor to use in creating the migration file.',
				'Example: --editor=nano'
			);
	}

	/**
	 * Simple helper function to check validity of provided extension name
	 *
	 * @return bool - whether or not extension is valid
	 **/
	private function isValidExtension($extension)
	{
		$ext = explode("_", $extension);
		$dir = '';

		switch ($ext[0])
		{
			case 'com':
				$dir = JPATH_ROOT . DS . 'components' . DS . $extension;
			break;
			case 'mod':
				$dir = JPATH_ROOT . DS . 'modules' . DS . $extension;
			break;
			case 'plg':
				$dir = JPATH_ROOT . DS . 'plugins' . DS . $ext[1] . DS . $ext[2];
			break;
		}

		return (is_dir($dir)) ? true : false;
	}

	/**
	 * Craft our query - based on arguments provided
	 *
	 * @return (object) $this - for method chaining
	 **/
	private function getQueryType()
	{
		if ($table = $this->arguments->getOpt('table'))
		{
			// Check if table exists
			$dbo    = \JFactory::getDbo();
			$prefix = $dbo->getPrefix();
			$table  = str_replace($prefix, '#__', $table);

			if (!in_array(str_replace('#__', $prefix, $table), $dbo->getTableList()))
			{
				$this->addReplacement('query_up', '$^create.table^$');
				$this->addReplacement('query_down', '$^drop.table^$');
				$this->addReplacement('table_name', $table);

				if ($fields = $this->arguments->getOpt('fields'))
				{
					$this->parseFields($fields);
				}
			}
			else
			{
				$this->addReplacement('query_up', '');
				$this->addReplacement('query_down', '');
			}
		}
		else
		{
			$this->addReplacement('query_up', '');
			$this->addReplacement('query_down', '');
		}

		return $this;
	}

	/**
	 * Parse incoming fields var into something usable
	 *
	 * @return void
	 **/
	private function parseFields($fields)
	{
		$parsed = array();
		$fields = explode(',', $fields);
		$i      = 0;

		foreach ($fields as $field)
		{
			$field  = trim($field);
			$parts  = explode("=>", $field);

			$parsed[] = array(
				'field_name'      => $parts[0],
				'field_data_type' => $parts[1],
				'field_null'      => ((isset($parts[2])) ? $parts[2] : 'NOT NULL'),
				'field_default'   => ((isset($parts[3])) ? $parts[3] : '')
			);

			if ($i == 0)
			{
				$this->addReplacement('pk_field_name', $parts[0]);
			}
			$i++;
		}

		$this->addReplacement('field', $parsed);
	}
}