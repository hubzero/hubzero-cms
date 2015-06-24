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

namespace Components\Config\Controllers;

use Components\Config\Models;
use Hubzero\Component\AdminController;
use Exception;
use Component;
use Notify;
use Route;
use User;
use App;

include_once(JPATH_COMPONENT . DS . 'models' . DS . 'application.php');

/**
 * Controller class for the Application config
 */
class Application extends AdminController
{
	/**
	 * Class Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get the document object.
		$document = App::get('document');

		$model = new Models\Application();

		// Access check.
		if (!User::authorise('core.admin', $model->getState('component.option')))
		{
			App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$form = $model->getForm();
		$data = $model->getData();

		// Check for model errors.
		if ($errors = $model->getErrors())
		{
			App::abort(500, implode('<br />', $errors));
		}

		// Bind the form to the data.
		if ($form && $data)
		{
			$form->bind($data);
		}

		// Get the params for com_users.
		$usersParams = Component::params('com_users');

		// Get the params for com_media.
		$mediaParams = Component::params('com_media');

		// Load settings for the FTP layer.
		$ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view
			->set('model', $model)
			->set('form', $form)
			->set('data', $data)
			->set('ftp', $ftp)
			->set('usersParams', $usersParams)
			->set('mediaParams', $mediaParams)
			->set('document', $document)
			->setLayout('default')
			->display();
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin'))
		{
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_ALERTNOAUTHOR')
			);
			return;
		}

		// Set FTP credentials, if given.
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$model = new Models\Application();
		$form  = $model->getForm();
		$data  = Request::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					Notify::warning($errors[$i]->getMessage());
				}
				else
				{
					Notify::warning($errors[$i]);
				}
			}

			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=application', false)
			);
			return false;
		}

		// Attempt to save the configuration.
		$data   = $return;
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=application', false),
				Lang::txt('JERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return false;
		}

		// Set the success message.
		$message = Lang::txt('COM_CONFIG_SAVE_SUCCESS');

		// Set the redirect based on the task.
		switch (Request::getCmd('task'))
		{
			case 'apply':
				App::redirect(Route::url('index.php?option=' . $this->_option, false), $message);
				break;

			case 'save':
			default:
				App::redirect(Route::url('index.php', false), $message);
				break;
		}
	}

	/**
	 * Cancel operation
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin', 'com_config'))
		{
			App::redirect(Route::url('index.php', false), Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Set FTP credentials, if given
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Clean the session data.
		User::setState('com_config.config.global.data', null);

		App::redirect(Route::url('index.php', false));
	}

	/**
	 * Refresh the help
	 *
	 * @return  void
	 */
	public function refreshHelp()
	{
		// Set FTP credentials, if given
		\JClientHelper::setCredentialsFromRequest('ftp');

		if (($data = file_get_contents('http://help.joomla.org/helpsites.xml')) === false)
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_ERROR_HELPREFRESH_FETCH'), 'error');
		}
		elseif (!\Filesystem::write(JPATH_BASE . '/help/helpsites.xml', $data))
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_ERROR_HELPREFRESH_ERROR_STORE'), 'error');
		}
		else
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_HELPREFRESH_SUCCESS'));
		}
	}

	/**
	 * Method to remove the root property from the configuration.
	 *
	 * @return  bool  True on success, false on failure.
	 */
	public function removerootTask()
	{
		// Check for request forgeries.
		\Session::checkToken('get');

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin'))
		{
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_ALERTNOAUTHOR')
			);
			return;
		}

		// Initialise model.
		$model = new Models\Application();

		// Attempt to save the configuration and remove root.
		$return = $model->removeroot();

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return;
		}

		// Set the redirect based on the task.
		App::redirect(
			Route::url('index.php', false),
			Lang::txt('COM_CONFIG_SAVE_SUCCESS')
		);
	}
}
