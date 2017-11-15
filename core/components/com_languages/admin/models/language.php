<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

jimport('joomla.application.component.modeladmin');

/**
 * Languages Component Language Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.5
 */
class LanguagesModelLanguage extends JModelAdmin
{
	/**
	 * Override to get the table
	 *
	 * @return	JTable
	 * @since	1.6
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		return JTable::getInstance('Language');
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState()
	{
		$params = Component::params('com_languages');

		// Load the User state.
		$langId = (int) Request::getInt('lang_id');
		$this->setState('language.id', $langId);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to get a member item.
	 *
	 * @param	integer	The id of the member to get.
	 *
	 * @return	mixed	User data object on success, false on failure.
	 * @since	1.0
	 */
	public function getItem($langId = null)
	{
		// Initialise variables.
		$langId = (!empty($langId)) ? $langId : (int) $this->getState('language.id');
		$false  = false;

		// Get a member row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($langId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return $false;
		}

		// Set a valid accesslevel in case '0' is stored due to a bug in the installation SQL.
		if ($table->access == '0')
		{
			$table->access = (int) Config::get('access');
		}

		$properties = $table->getProperties(1);
		$value = \Hubzero\Utility\Arr::toObject($properties, '\\Hubzero\\Base\\Obj');

		return $value;
	}

	/**
	 * Method to get the group form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_languages.language', 'language', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = User::getState('com_languages.edit.language.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data)
	{
		$langId = (int) $this->getState('language.id');
		$isNew  = true;

		$table = $this->getTable();

		// Load the row if saving an existing item.
		if ($langId > 0)
		{
			$table->load($langId);
			$isNew = false;
		}

		// Prevent white spaces, including East Asian double bytes
		$spaces = array('/\xE3\x80\x80/', ' ');

		$data['lang_code'] = str_replace($spaces, '', $data['lang_code']);
		$data['sef'] = str_replace($spaces, '', $data['sef']);

		// Bind the data
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}

		// Check the data
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		$result = Event::trigger('extension.onExtensionBeforeSave', array('com_languages.language', &$table, $isNew));

		// Check the event responses.
		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}

		// Trigger the onExtensionAfterSave event.
		Event::trigger('extension.onExtensionAfterSave', array('com_languages.language', &$table, $isNew));

		$this->setState('language.id', $table->lang_id);

		// Clean the cache.
		$this->cleanCache();

		return true;
	}

	/**
	 * Custom clean cache method
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('_system');
		parent::cleanCache('com_languages');
	}
}
