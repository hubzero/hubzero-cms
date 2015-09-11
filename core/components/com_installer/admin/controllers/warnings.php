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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Models;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'warnings.php');

/**
 * Controller for discovering extensions
 */
class Warnings extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Warnings();

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view->messages = $model->getItems();
		$this->view->state    = $model->getState();

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
}
