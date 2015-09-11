<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Models;
use Route;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'database.php');

/**
 * Controller for database
 */
class Database extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Database();

		// Get data from the model
		$this->view->state         = $model->getState();
		$this->view->changeSet     = $model->getItems();
		$this->view->errors        = $this->view->changeSet->check();
		$this->view->results       = $this->view->changeSet->getStatus();
		$this->view->schemaVersion = $this->getSchemaVersion();
		$this->view->updateVersion = $this->getUpdateVersion();
		$this->view->filterParams  = $this->getDefaultTextFilters();
		$this->view->schemaVersion = ($this->view->schemaVersion) ?  $this->view->schemaVersion : Lang::txt('JNONE');
		$this->view->updateVersion = ($this->view->updateVersion) ?  $this->view->updateVersion : Lang::txt('JNONE');
		$this->view->pagination    = $model->getPagination();
		$this->view->errorCount    = count($this->errors);

		$errors = count($this->view->errors);
		if ($this->view->schemaVersion != $this->view->changeSet->getSchema())
		{
			$this->view->errorCount++;
		}
		if (!$this->view->filterParams)
		{
			$this->view->errorCount++;
		}
		if (version_compare($this->view->updateVersion, JVERSION) != 0)
		{
			$this->view->errorCount++;
		}

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$showMessage = false;
		if (is_object($this->view->state))
		{
			$message1    = $this->view->state->get('message');
			$message2    = $this->view->state->get('extension_message');
			$showMessage = ($message1 || $message2);
		}
		$this->view->showMessage = $showMessage;

		$this->view->display();
	}

	/**
	 * Tries to fix missing database updates
	 *
	 * @return  void
	 */
	public function fixTask()
	{
		$model = new Models\Database();
		$model->fix();

		App::redirect(
			Route::url('index.php?option=com_installer&view=database', false)
		);
	}
}
