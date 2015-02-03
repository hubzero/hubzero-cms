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
		$document = \JFactory::getDocument();

		$model = new Models\Application();

		// Access check.
		if (!$this->juser->authorise('core.admin', $model->getState('component.option')))
		{
			return \JError::raiseWarning(404, \JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$form = $model->getForm();
		$data = $model->getData();

		// Check for model errors.
		if ($errors = $model->getErrors())
		{
			\JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Bind the form to the data.
		if ($form && $data)
		{
			$form->bind($data);
		}

		// Get the params for com_users.
		$usersParams = \JComponentHelper::getParams('com_users');

		// Get the params for com_media.
		$mediaParams = \JComponentHelper::getParams('com_media');

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
		\JRequest::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		// Check if the user is authorized to do this.
		if (!$this->juser->authorise('core.admin'))
		{
			$this->setRedirect('index.php', \JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Set FTP credentials, if given.
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app   = \JFactory::getApplication();
		$model = new Models\Application();
		$form  = $model->getForm();
		$data  = \JRequest::getVar('jform', array(), 'post', 'array');

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
			$this->setRedirect(\JRoute::_('index.php?option=' . $this->_option . '&view=application', false));
			return false;
		}

		// Attempt to save the configuration.
		$data   = $return;
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState($this->_option . '.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = \JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=' . $this->_option . '&view=application', $message, 'error');
			return false;
		}

		// Set the success message.
		$message = \JText::_('COM_CONFIG_SAVE_SUCCESS');

		// Set the redirect based on the task.
		switch (\JRequest::getCmd('task'))
		{
			case 'apply':
				$this->setRedirect('index.php?option=' . $this->_option, $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php', $message);
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
		if (!$this->juser->authorise('core.admin', 'com_config'))
		{
			$this->setRedirect('index.php', \JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Set FTP credentials, if given
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Clean the session data.
		\JFactory::getApplication()->setUserState('com_config.config.global.data', null);

		$this->setRedirect('index.php');
	}

	/**
	 * Refresh the help
	 *
	 * @return  void
	 */
	public function refreshHelp()
	{
		jimport('joomla.filesystem.file');

		// Set FTP credentials, if given
		\JClientHelper::setCredentialsFromRequest('ftp');

		if (($data = file_get_contents('http://help.joomla.org/helpsites.xml')) === false)
		{
			$this->setRedirect('index.php?option=com_config', \JText::_('COM_CONFIG_ERROR_HELPREFRESH_FETCH'), 'error');
		}
		elseif (!\JFile::write(JPATH_BASE . '/help/helpsites.xml', $data))
		{
			$this->setRedirect('index.php?option=com_config', \JText::_('COM_CONFIG_ERROR_HELPREFRESH_ERROR_STORE'), 'error');
		}
		else
		{
			$this->setRedirect('index.php?option=com_config', \JText::_('COM_CONFIG_HELPREFRESH_SUCCESS'));
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
		\JSession::checkToken('get') or die('Invalid Token');

		// Check if the user is authorized to do this.
		if (!$this->juser->authorise('core.admin'))
		{
			\JFactory::getApplication()->redirect('index.php', \JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Initialise model.
		$model = new Models\Application(); //$this->getModel('Application');

		// Attempt to save the configuration and remove root.
		$return = $model->removeroot();

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			$this->setRedirect(
				'index.php',
				\JText::sprintf('JERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return;
		}

		// Set the redirect based on the task.
		$this->setRedirect('index.php', \JText::_('COM_CONFIG_SAVE_SUCCESS'));
	}
}
