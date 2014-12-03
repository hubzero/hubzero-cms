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

namespace Hubzero\Console\Command\Repository;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Content\Migration\Base as Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Repository flavor class
 **/
class Flavor extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Set the flavor
	 *
	 * @return void
	 **/
	public function set()
	{
		if (!$flavor = $this->arguments->getOpt(3))
		{
			$this->output->error('Please provide the flavor you would like to use');
		}

		$database  = \JFactory::getDbo();
		$migration = new Migration($database);

		switch ($flavor)
		{
			case 'amazon':
				// Disable com_tools
				$migration->disableComponent('com_tools');
				$this->output->addLine('Disabling com_tools');

				// Disable tool-related modules
				$migration->disableModule('mod_mytools');
				$this->output->addLine('Disabling mod_mytools');
				$migration->disableModule('mod_mycontributions');
				$this->output->addLine('Disabling mod_contributions');
				$migration->disableModule('mod_mysessions');
				$this->output->addLine('Disabling mod_mysessions');

				$defaults = array(
					'{"module":44,"col":1,"row":1,"size_x":1,"size_y":2}',
					'{"module":35,"col":1,"row":3,"size_x":1,"size_y":2}',
					'{"module":38,"col":1,"row":5,"size_x":1,"size_y":2}',
					'{"module":39,"col":1,"row":7,"size_x":1,"size_y":2}',
					'{"module":33,"col":2,"row":1,"size_x":1,"size_y":2}',
					'{"module":42,"col":2,"row":3,"size_x":1,"size_y":2}',
					'{"module":34,"col":2,"row":5,"size_x":1,"size_y":2}',
					'{"module":37,"col":3,"row":1,"size_x":1,"size_y":2}'
				);

				$params = array(
					"allow_customization" => "1",
					"position"            => "memberDashboard",
					"defaults"            => '[' . implode(',', $defaults) . ']'
				);

				$migration->savePluginParams('members', 'dashboard', $params);
				$this->output->addLine('Updating default members dashboard configuration');

				// Update kb articles
				$query = "UPDATE `#__faq_categories` SET `state` = 2 WHERE `alias` = 'tools'";
				$database->setQuery($query);
				$database->query();
				$query = "UPDATE `#__faq` SET `state` = 2 WHERE `alias` = 'webdav'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Deleting tool and webdav related KB articles');

				// Set amazon param in welcome template
				$params = array('flavor' => 'amazon', 'template' => 'hubbasic2013');
				$query  = "UPDATE `#__template_styles` SET `params` = " . $database->quote(json_encode($params)) . " WHERE `template` = 'welcome'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Setting amazon flavor flag in welcome template');

				break;

			case 'default':
			case 'vanilla':
			case 'grape':
				// Enable com_tools
				$migration->enableComponent('com_tools');
				$this->output->addLine('Enabling com_tools');

				// Enable tool-related modules
				$migration->enableModule('mod_mytools');
				$this->output->addLine('Enabling mod_mytools');
				$migration->enableModule('mod_mycontributions');
				$this->output->addLine('Enabling mod_mycontributions');
				$migration->enableModule('mod_mysessions');
				$this->output->addLine('Enabling mod_mysessions');

				$defaults = array(
					'{"module":44,"col":1,"row":1,"size_x":1,"size_y":2}',
					'{"module":35,"col":1,"row":3,"size_x":1,"size_y":2}',
					'{"module":38,"col":1,"row":5,"size_x":1,"size_y":2}',
					'{"module":39,"col":1,"row":7,"size_x":1,"size_y":2}',
					'{"module":33,"col":2,"row":1,"size_x":1,"size_y":2}',
					'{"module":42,"col":2,"row":3,"size_x":1,"size_y":2}',
					'{"module":34,"col":2,"row":5,"size_x":1,"size_y":2}',
					'{"module":41,"col":3,"row":1,"size_x":1,"size_y":2}',
					'{"module":36,"col":3,"row":3,"size_x":1,"size_y":2}',
					'{"module":37,"col":3,"row":5,"size_x":1,"size_y":2}'
				);

				$params = array(
					"allow_customization" => "1",
					"position"            => "memberDashboard",
					"defaults"            => '[' . implode(',', $defaults) . ']'
				);

				$migration->savePluginParams('members', 'dashboard', $params);
				$this->output->addLine('Restoring default members dashboard configuration');

				// Update kb articles
				$query = "UPDATE `#__faq_categories` SET `state` = 1 WHERE `alias` = 'tools'";
				$database->setQuery($query);
				$database->query();
				$query = "UPDATE `#__faq` SET `state` = 1 WHERE `alias` = 'webdav'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Restoring tool and webdav related KB articles');

				// Set flavor param in welcome template
				$params = array('flavor' => '', 'template' => 'hubbasic2013');
				$query  = "UPDATE `#__template_styles` SET `params` = " . $database->quote(json_encode($params)) . " WHERE `template` = 'welcome'";
				$database->setQuery($query);
				$database->query();
				$this->output->addLine('Unsetting flavor flag in welcome template');

				break;

			default:
				$this->output->error('Flavor provided is unknown.');
				break;
		}

		$this->output->addLine("Successfully updated to the {$flavor} flavor!", 'success');
	}



	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview(
		         'Repository management functions used to set the "flavor" of the hub.
		         Use this command to setup/convert your hub to one of the predefined
		         flavors. This often includes configuration changes and enabling/disabling
		         certain components based on the needs and limitations of the given
		         environement.'
		     )
		     ->noArgsSection()
		     ->addSection('Usage')
		     ->addArgument(
		         'muse repository:flavor set [flavor_name]'
		     )
		     ->addSpacer()
		     ->addSection('Flavors')
		     ->addArgument(
		         'amazon',
		         'This flavor customizes the hub uniquely for use in the Amazon EC2
		         environement. This primarily includes disabling tools and tool related
		         functions and content.'
		     )
		     ->addArgument(
		         'default',
		         'This is the default hub install.'
		     )
		     ->render();
	}
}