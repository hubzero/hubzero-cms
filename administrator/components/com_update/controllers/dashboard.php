<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Update controller class
 */
class UpdateControllerDashboard extends \Hubzero\Component\AdminController
{
	/**
	 * Display the update dashboard
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$config = JFactory::getConfig();

		$this->view->repositoryVersion   = json_decode(cli::version());
		$this->view->repositoryVersion   = $this->view->repositoryVersion[0];
		$this->view->repositoryMechanism = json_decode(cli::mechanism());
		$this->view->repositoryMechanism = $this->view->repositoryMechanism[0];
		$this->view->databaseMechanism   = $config->get('dbtype');
		$this->view->databaseVersion     = JFactory::getDbo()->getVersion();
		$this->view->status    = json_decode(cli::status());
		$this->view->upcoming  = json_decode(cli::update(true));
		$this->view->migration = json_decode(cli::migration());

		// Output the HTML
		$this->view->display();
	}
}