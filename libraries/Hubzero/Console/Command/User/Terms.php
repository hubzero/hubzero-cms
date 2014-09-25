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

namespace Hubzero\Console\Command\User;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * User class for terms of use functions
 **/
class Terms extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for user command
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('Functions for updating hub user terms of use.')
		     ->render();
	}

	/**
	 * Clear terms of use agreements
	 * 
	 * @return void
	 */
	public function clear()
	{
		// Initialize confirm
		$confirm = 'no';

		if (!$this->output->isInteractive() && !$this->arguments->getOpt('f'))
		{
			$this->output->addLine('To forcibly clear all terms of use agreements for all users, please provide the -f flag. This action is irreversable.', 'warning');
			return;
		}
		else if (!$this->output->isInteractive() && $this->arguments->getOpt('f'))
		{
			$confirm = 'yes';
		}
		else if ($this->output->isInteractive())
		{
			// Confirm clearing
			$confirm = $this->output->getResponse('Are you sure you want to clear Terms of Use for all users? This will also require users to agree to new terms with next login? (yes/no)');
		}

		// Did we get a yes?
		if (strtolower($confirm) == 'yes' || strtolower($confirm) == 'y')
		{
			// Get db object
			$dbo = \JFactory::getDbo();

			// Update registration config value to require re-agreeing upon next login
			$params = \JComponentHelper::getParams('com_members');
			$currentTOU = $params->get('registrationTOU','RHRH');
			$newTOU     = substr_replace($currentTOU, 'R', 3);
			$params->set('registrationTOU', $newTOU);

			// Update registration param in db
			$query = "UPDATE `#__extensions` SET `params`=" . $dbo->quote($params->toString()) . " WHERE `name`='com_members'";
			$dbo->setQuery($query);
			if (!$dbo->query())
			{
				$this->output->error('Unable to set registration field TOU to required on next update.');
			}

			// Clear all old TOU states
			$dbo->setQuery("UPDATE `#__xprofiles` SET `usageAgreement`=0;");
			if (!$dbo->query())
			{
				$this->output->error('Unable to clear xprofiles terms of use.');
			}

			// Output message to let admin know everything went well
			$this->output->addLine('Terms of Use successfully cleared & registration param updated!', 'success');
		}
		else
		{
			$this->output->addLine('Operation aborted.');
		}
	}
}