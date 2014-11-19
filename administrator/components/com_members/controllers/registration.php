<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'extension.php');

/**
 * Controller class for registration configuration
 */
class MembersControllerRegistration extends \Hubzero\Component\AdminController
{
	/**
	 * Display configurations for registration
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$config = new JForm('com_members.registration');
		$config->loadFile(JPATH_COMPONENT . DS . 'config.xml', true, '/config');
		$config->bind($this->config->toArray());

		$this->config = $config;

		$this->view->params = $config->getFieldset('registration');

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save changes to the registration
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$settings = JRequest::getVar('settings', array(), 'post');

		if (!is_array($settings) || empty($settings))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_MEMBERS_REGISTRATION_ERROR_MISSING_DATA'),
				'error'
			);
			return;
		}

		$arr = array();

		$component = new JTableExtension($this->database);
		$component->load($component->find(array('element' => $this->_option, 'type' => 'component')));

		$params = new JRegistry($component->params);

		foreach ($settings as $name => $value)
		{
			$r = $value['create'] . $value['proxy'] . $value['update'] . $value['edit'];

			$params->set('registration' . trim($name), trim($r));
		}

		$component->params = $params->__toString();
		$component->store();

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_MEMBERS_REGISTRATION_SAVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
