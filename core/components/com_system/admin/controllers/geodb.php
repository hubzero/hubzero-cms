<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

/**
 * System controller class for info
 */
class Geodb extends AdminController
{
	/**
	 * Default view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Import the hub configuration
	 *
	 * @return  void
	 */
	public function importHubconfigTask()
	{
		if (file_exists(PATH_APP . DS . 'hubconfiguration.php'))
		{
			include_once(PATH_APP . DS . 'hubconfiguration.php');
		}

		$table = new \JTableExtension($this->database);
		$table->load($table->find(array(
			'element' => $this->_option,
			'type'    => 'component'
		)));

		if (class_exists('HubConfig'))
		{
			$hub_config = new \HubConfig();

			$this->config->set('geodb_driver', $hub_config->ipDBDriver);
			$this->config->set('geodb_host', $hub_config->ipDBHost);
			$this->config->set('geodb_port', $hub_config->ipDBPort);
			$this->config->set('geodb_user', $hub_config->ipDBUsername);
			$this->config->set('geodb_password', $hub_config->ipDBPassword);
			$this->config->set('geodb_database', $hub_config->ipDBDatabase);
			$this->config->set('geodb_prefix', $hub_config->ipDBPrefix);
		}

		$table->params = $this->config->toString();

		$table->store();

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_SYSTEM_GEO_IMPORT_COMPLETE')
		);
	}
}
