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
use Request;
use Notify;
use Lang;
use Html;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'manage.php');

/**
 * Controller for managing extensions
 */
class Manage extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('publish', 'publish');

		parent::execute();
	}

	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Manage();

		$this->view->state      = $model->getState();
		$this->view->items      = $model->getItems();
		$this->view->pagination = $model->getPagination();
		$this->view->form       = $model->getForm();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
		}

		//Check if there are no matching items
		if (!count($this->view->items))
		{
			Notify::warning(Lang::txt('COM_INSTALLER_MSG_MANAGE_NOEXTENSION'));
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

		// Include the component HTML helpers.
		Html::addIncludePath(dirname(__DIR__) . '/helpers/html');

		$this->view->display();
	}

	/**
	 * Enable/Disable an extension (if supported).
	 *
	 * @since	1.6
	 */
	public function publishTask()
	{
		// Check for request forgeries.
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids    = Request::getVar('cid', array(), '', 'array');
		$values = array('publish' => 1, 'unpublish' => 0);
		$task   = $this->getTask();
		$value  = \Hubzero\Utility\Arr::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			App::abort(500, Lang::txt('COM_INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = new Models\Manage();

			// Change the state of the records.
			if (!$model->publish($ids, $value))
			{
				App::abort(500, implode('<br />', $model->getErrors()));
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_UNPUBLISHED';
				}
				Notify::success(Lang::txts($ntext, count($ids)));
			}
		}

		App::redirect(Route::url('index.php?option=com_installer&controller=manage', false));
	}

	/**
	 * Remove an extension (Uninstall).
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$eid   = Request::getVar('cid', array(), '', 'array');
		$model = new Models\Manage();

		\Hubzero\Utility\Arr::toInteger($eid, array());
		$result = $model->remove($eid);
		App::redirect(Route::url('index.php?option=com_installer&controller=manage', false));
	}

	/**
	 * Refreshes the cached metadata about an extension.
	 *
	 * Useful for debugging and testing purposes when the XML file might change.
	 *
	 * @since	1.6
	 */
	public function refreshTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$uid   = Request::getVar('cid', array(), '', 'array');
		$model = new Models\Manage();

		\Hubzero\Utility\Arr::toInteger($uid, array());
		$result = $model->refresh($uid);

		App::redirect(Route::url('index.php?option=com_installer&controller=manage', false));
	}

	/**
	 * Creates the content for the tooltip which shows compatibility information
	 *
	 * @var  string  $system_data  System_data information
	 *
	 * @since  2.5.28
	 *
	 * @return  string  Content for tooltip
	 */
	protected function createCompatibilityInfo($system_data)
	{
		$system_data = json_decode($system_data);

		if (empty($system_data->compatibility))
		{
			return '';
		}

		$compatibility = $system_data->compatibility;

		$info = Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_INSTALLED',
					$compatibility->installed->version,
					implode(', ', $compatibility->installed->value)
				)
				. '<br />'
				. Lang::txt('COM_INSTALLER_COMPATIBILITY_TOOLTIP_AVAILABLE',
					$compatibility->available->version,
					implode(', ', $compatibility->available->value)
				);

		return $info;
	}
}
