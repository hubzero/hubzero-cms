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

include_once(JPATH_COMPONENT . DS . 'models' . DS . 'component.php');

/**
 * Controller class for a component's config
 */
class Component extends AdminController
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
	 * Displa the configuration.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Component();

		// Access check.
		if (!User::authorise('core.admin', $model->getState('component.option')))
		{
			return \JError::raiseWarning(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$form = $model->getForm();
		$component = $model->getComponent();

		// Check for errors.
		if (count($errors = $this->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
			return false;
		}

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		// Get the document object.
		$document = \JFactory::getDocument();
		$document->setTitle(Lang::txt('JGLOBAL_EDIT_PREFERENCES'));

		Request::setVar('hidemainmenu', true);

		$this->view
			->set('model', $model)
			->set('form', $form)
			->set('component', $component)
			->set('document', $document)
			->setLayout('default')
			->display();
	}

	/**
	 * Save the configuration
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		\JSession::checkToken() or jexit(Lang::txt('JINVALID_TOKEN'));

		// Set FTP credentials, if given.
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app    = \JFactory::getApplication();
		$model  = new Models\Component(); //$this->getModel('Component');
		$form   = $model->getForm();
		$data   = Request::getVar('jform', array(), 'post', 'array');
		$id     = Request::getInt('id');
		$option = Request::getCmd('component');

		// Check if the user is authorized to do this.
		if (!$this->juser->authorise('core.admin', $option))
		{
			$this->setRedirect('index.php', \Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($this->_option . '.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component', false)
			);
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id'     => $id,
			'option' => $option
		);
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState($this->_option . '.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = \Lang::txt('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component', false),
				$message,
				'error'
			);
			return false;
		}

		// Set the redirect based on the task.
		switch (Request::getCmd('task'))
		{
			case 'apply':
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&refresh=1', false),
					Lang::txt('COM_CONFIG_SAVE_SUCCESS')
				);
				break;

			case 'save':
			default:
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&view=close&tmpl=component', false)
				);
				break;
		}
	}
}
