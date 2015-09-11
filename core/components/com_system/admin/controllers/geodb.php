<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
