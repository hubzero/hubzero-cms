<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
			$path = Filesystem::cleanPath($path);
			\Hubzero\Filesystem\Util::checkPath(PATH_ROOT . DS . $path);
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
			\JForm::addFormPath(PATH_ROOT . DS . $path);
		}
		else
		{
			// Add the search path for the admin component config.xml file.
			\JForm::addFormPath(\Component::path($this->getState('component.option')));
		}
		\JForm::addFormPath(\Component::path($this->getState('component.option')) . '/config');

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
		Lang::load($option, PATH_APP . "/bootstrap/administrator", null, false, true)
		|| Lang::load($option, \Component::path($option) . "/admin", null, false, true)
		|| Lang::load($option, \Component::path($option) . "/site", null, false, true);

		if ($path = $this->getState('component.path'))
		{
			Lang::load($option, PATH_ROOT . dirname($path) . DS . 'Admin', null, false, true);
			Lang::load($option, PATH_ROOT . dirname($path) . DS . 'Site', null, false, true);
		}

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
