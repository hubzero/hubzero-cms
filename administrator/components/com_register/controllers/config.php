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

ximport('Hubzero_Controller');

/**
 * Controller class for registration configuration
 */
class RegisterControllerConfig extends Hubzero_Controller
{
	/**
	 * Display configurations for registration
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$config = new JParameter($this->config->toString());
			$config->loadSetupFile(JPATH_COMPONENT . DS . 'config.xml');
			$this->config = $config;
		}
		else
		{
			$this->config->loadSetupFile(JPATH_COMPONENT . DS . 'config.xml');
		}
		$this->view->params = $this->config->renderToArray();

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
	 * @return     void
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
				JText::_('No data to save'),
				'error'
			);
			return;
		}

		$arr = array();

		$component = new JTableComponent($this->database);
		$component->loadByOption($this->_option);

		$params = trim($component->params);
		if ($params) 
		{
			$params = explode("\n", $params);
			foreach ($params as $p)
			{
				$b = explode('=', $p);
				$arr[$b[0]] = trim(end($b));
			}
		}
		foreach ($settings as $name => $value)
		{
			$r = $value['create'] . $value['proxy'] . $value['update'] . $value['edit'];

			$arr['registration' . trim($name)] = trim($r);
		}
		$a = array();
		foreach ($arr as $k => $v)
		{
			$a[] = $k . '=' . $v;
		}
		$component->params = implode("\n", $a);
		$component->store();

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Configuration saved')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
}
