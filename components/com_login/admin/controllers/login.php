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

namespace Components\Login\Admin\Controllers;

use Components\Login\Models\Login as Model;
use Hubzero\Component\AdminController;
use Exception;

/**
 * Login Controller
 */
class Login extends AdminController
{
	/**
	 * Default task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Special treatment is required for this plugin, as this view may be called
		// after a session timeout. We must reset the view and layout prior to display
		// otherwise an error will occur.

		\JRequest::setVar('view', 'login');
		//\JRequest::setVar('layout', 'default');

		$this->view
			->setLayout('default')
			->display();
	}

	/**
	 * Method to log in a user.
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		// Check for request forgeries.
		\JSession::checkToken('request') or jexit(\Lang::txt('JINVALID_TOKEN'));

		$model = new Model();
		$model->setState('task', $this->_task);

		$credentials = $model->getState('credentials');
		$return      = $model->getState('return');

		$app = \JFactory::getApplication();
		$result = $app->login($credentials, array('action' => 'core.login.admin'));

		if (!($result instanceof Exception))
		{
			$app->redirect($return);
		}

		$this->displayTask();
	}

	/**
	 * Method to log out a user.
	 *
	 * @return  void
	 */
	public function logoutTask()
	{
		\JSession::checkToken('request') or jexit(\Lang::txt('JInvalid_Token'));

		$app = \JFactory::getApplication();

		$userid = \JRequest::getInt('uid', null);

		$result = $app->logout($userid, array(
			'clientid' => ($userid ? 0 : 1)
		));

		if (!($result instanceof Exception))
		{
			$model = new Model();
			$model->setState('task', $this->_task);

			$return = $model->getState('return');
			$app->redirect($return);
		}

		$this->displayTask();
	}
}
