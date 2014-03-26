<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Plugin controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.6
 */
class PluginsControllerPlugin extends JControllerForm
{
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   11.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		$result = parent::edit($key, $urlVar);

		if ($component = JRequest::getVar('component', ''))
		{
			$this->redirect .= '&component=' . $component;
		}

		return $result;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModel  &$model     The data model object.
	 * @param   array   $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function postSaveHook(JModel &$model, $validData = array())
	{
		if ($component = JRequest::getVar('component', ''))
		{
			// Redirect to the list screen.
			$this->setRedirect(
				'index.php?option=com_' . $component . '&controller=plugins'
			);
		}
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   11.1
	 */
	public function cancel($key = null)
	{
		$result = parent::cancel($key);

		if ($component = JRequest::getVar('component', ''))
		{
			// Redirect to the list screen.
			$this->setRedirect(
				'index.php?option=com_' . $component . '&controller=plugins'
			);
		}

		return $result;
	}
}
