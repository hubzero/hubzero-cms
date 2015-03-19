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

namespace Components\Forum\Admin\Models;

jimport('joomla.application.component.modeladmin');

/**
 * Category model.
 *
 * NOTE: This is used purely for interfacing with
 * Joomla's permissions system.
 */
class AdminCategory extends \JModelAdmin
{
	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 * @since   11.1
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = \JFactory::getApplication('administrator');
		$table = $this->getTable();
		$key = $table->getKeyName();

		// Get the pk of the record from the request.
		$pk = \JRequest::getVar($key, array());
		if (!empty($pk))
		{
			$pk = intval($pk[0]);
		}
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$value = \Component::params($this->option);
		$this->setState('params', $value);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_forum.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
		 * Returns a reference to the a Table object, always creating it.
		 *
		 * @param	type	The table type to instantiate
		 * @param	string	A prefix for the table class name. Optional.
		 * @param	array	Configuration array for model. Optional.
		 * @return	JTable	A database object
		 * @since	1.7
		 */
	public function getTable($type = 'Category', $prefix = 'ForumTable', $config = array())
	{
		$db = \JFactory::getDBO();
		return new \Components\Forum\Tables\Category($db); //JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 * @since   11.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = \JFactory::getApplication()->getUserState('com_forum.edit.category.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}
}
