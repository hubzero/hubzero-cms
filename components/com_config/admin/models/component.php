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

namespace Components\Config\Models;

jimport('joomla.application.component.modelform');

/**
 * Model class for Component config
 */
class Component extends \JModelForm
{
	/**
	 * The event to trigger before saving the data.
	 *
	 * @var    string
	 * @since  2.5.10
	 */
	protected $event_before_save = 'onConfigurationBeforeSave';

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var    string
	 * @since  2.5.10
	 */
	protected $event_after_save = 'onConfigurationAfterSave';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function populateState()
	{
		// Set the component (option) we are dealing with.
		$component = Request::getCmd('component');
		$this->setState('component.option', $component);

		// Set an alternative path for the configuration file.
		if ($path = Request::getString('path'))
		{
			$path = \JPath::clean(JPATH_SITE . '/' . $path);
			\JPath::check($path);
			$this->setState('component.path', $path);
		}
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed    A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if ($path = $this->getState('component.path'))
		{
			// Add the search path for the admin component config.xml file.
			\JForm::addFormPath($path);
		}
		else
		{
			// Add the search path for the admin component config.xml file.
			\JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $this->getState('component.option'));
		}
		\JForm::addFormPath(JPATH_SITE . '/components/' . $this->getState('component.option') . '/config');

		// Get the form.
		$form = $this->loadForm(
			'com_config.component',
			'config',
			array('control' => 'jform', 'load_data' => $loadData),
			false,
			'/config'
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Get the component information.
	 *
	 * @return  object
	 * @since   1.6
	 */
	public function getComponent()
	{
		// Initialise variables.
		$option = $this->getState('component.option');

		// Load common and local language files.
		Lang::load($option, JPATH_BASE, null, false, true)
		|| Lang::load($option, JPATH_BASE . "/components/$option", null, false, true);

		return \Component::load($option);
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @param   array  An array containing all global config data.
	 * @return  bool   True on success, false on failure.
	 * @since   1.6
	 */
	public function save($data)
	{
		$table = \JTable::getInstance('extension');
		$isNew = true;

		// Save the rules.
		if (isset($data['params']) && isset($data['params']['rules']))
		{
			$rules = new \JAccessRules($data['params']['rules']);
			$asset = \JTable::getInstance('asset');

			if (!$asset->loadByName($data['option']))
			{
				$root = \JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store())
			{
				$this->setError($asset->getError());
				return false;
			}

			// We don't need this anymore
			unset($data['option']);
			unset($data['params']['rules']);
		}

		// Load the previous Data
		if (!$table->load($data['id']))
		{
			$this->setError($table->getError());
			return false;
		}

		unset($data['id']);

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the oonConfigurationBeforeSave event.
		$result = Event::trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		// Clean the component cache.
		$this->cleanCache('_system');

		// Trigger the onConfigurationAfterSave event.
		Event::trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));

		return true;
	}
}
