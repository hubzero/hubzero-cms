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

namespace Components\Redirect\Models;

jimport('joomla.application.component.modeladmin');

/**
 * Redirect link model.
 */
class Link extends \JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var  string
	 */
	protected $text_prefix = 'COM_REDIRECT';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object   $record  A record object.
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record)
	{
		if ($record->published != -2)
		{
			return false;
		}
		return \JFactory::getUser()->authorise('core.delete', 'com_redirect');
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object   $record  A record object.
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record)
	{
		// Check the component since there are no categories or other assets.
		return \JFactory::getUser()->authorise('core.edit.state', 'com_redirect');
	}


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 * @return  object  A database object
	*/
	public function getTable($type = 'Link', $prefix = 'RedirectTable', $config = array())
	{
		include_once(dirname(__DIR__) . DS . 'tables' . DS . 'link.php');
		$db = \JFactory::getDBO();
		return new \Components\Redirect\Tables\Link($db); //\JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  object   A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_redirect.link', 'link', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if ($this->canEditState((object) $data) != true)
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = \JFactory::getApplication()->getUserState('com_redirect.edit.link.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to activate links.
	 *
	 * @param   array    An array of link ids.
	 * @param   string   The new URL to set for the redirect.
	 * @param   string   A comment for the redirect links.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function activate(&$pks, $url, $comment = null)
	{
		// Initialise variables.
		$user = \JFactory::getUser();
		$db   = $this->getDbo();

		// Sanitize the ids.
		$pks = (array) $pks;
		\JArrayHelper::toInteger($pks);

		// Populate default comment if necessary.
		$comment = (!empty($comment)) ? $comment : \Lang::txt('COM_REDIRECT_REDIRECTED_ON', \JHtml::_('date', time()));

		// Access checks.
		if (!$user->authorise('core.edit', 'com_redirect'))
		{
			$pks = array();
			$this->setError(\Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			return false;
		}

		if (!empty($pks))
		{
			// Update the link rows.
			$db->setQuery(
				'UPDATE ' . $db->quoteName('#__redirect_links') .
				' SET ' . $db->quoteName('new_url') . ' = ' . $db->Quote($url) . ', ' . $db->quoteName('published') . ' = 1, ' . $db->quoteName('comment') . ' = ' . $db->Quote($comment) .
				' WHERE ' . $db->quoteName('id') . ' IN (' . implode(',', $pks) . ')'
			);
			$db->query();

			// Check for a database error.
			if ($error = $this->_db->getErrorMsg())
			{
				$this->setError($error);
				return false;
			}
		}
		return true;
	}
}
