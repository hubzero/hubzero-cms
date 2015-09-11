<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Update\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Update\Helpers\Cli;
use Component;
use Config;

/**
 * Update controller class
 */
class Dashboard extends AdminController
{
	/**
	 * Display the update dashboard
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$source = Component::params('com_update')->get('git_repository_source', null);

		$this->view->repositoryVersion   = json_decode(Cli::version());
		$this->view->repositoryVersion   = $this->view->repositoryVersion[0];
		$this->view->repositoryMechanism = json_decode(Cli::mechanism());
		$this->view->repositoryMechanism = $this->view->repositoryMechanism[0];
		$this->view->databaseMechanism   = Config::get('dbtype');
		$this->view->databaseVersion     = \App::get('db')->getVersion();
		$this->view->status    = json_decode(Cli::status());
		$this->view->upcoming  = json_decode(Cli::update(true, false, $source));
		$this->view->migration = json_decode(Cli::migration());

		if (!isset($this->view->repositoryMechanism))
		{
			$this->view->message = 'Please ensure that the component is properly configured';
			$this->view->setLayout('error');
		}
		elseif ($this->view->repositoryMechanism != 'GIT')
		{
			$this->view->message = 'The CMS update component currently only supports repositories managed via GIT';
			$this->view->setLayout('error');
		}

		// Output the HTML
		$this->view->display();
	}
}