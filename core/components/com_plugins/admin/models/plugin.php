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

namespace Components\Plugins\Admin\Models;

use Hubzero\Utility\Arr;
use Hubzero\Config\Registry;
use Exception;
use Filesystem;
use Request;
use Route;
use Lang;
use User;
use App;

jimport('joomla.application.component.modeladmin');

/**
 * Plugin model.
 */
class Plugin extends \JModelAdmin
{
	/**
	 * @var		string	The help screen key for the module.
	 * @since	1.6
	 */
	protected $helpKey = 'JHELP_EXTENSIONS_PLUGIN_MANAGER_EDIT';

	/**
	 * @var		string	The help screen base URL for the module.
	 * @since	1.6
	 */
	protected $helpURL;

	protected $_cache;

	/**
	 * The event to trigger after saving the data.
	 *
	 * @var  string
	 */
	protected $event_after_save = 'onExtensionAfterSave';

	/**
	 * The event to trigger after before the data.
	 *
	 * @var  string
	 */
	protected $event_before_save = 'onExtensionBeforeSave';

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  object   A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item    = $this->getItem();
			$folder  = $item->folder;
			$element = $item->element;
		}
		else
		{
			$folder  = Arr::getValue($data, 'folder', '', 'cmd');
			$element = Arr::getValue($data, 'element', '', 'cmd');
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.folder',  $folder);
		$this->setState('item.element', $element);

		// Get the form.
		$form = $this->loadForm('com_plugins.plugin', 'plugin', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
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
		$data = User::getState('com_plugins.edit.plugin.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('plugin.id');

		if (!isset($this->_cache[$pk]))
		{
			$false = false;

			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return $false;
			}

			// Convert to the Object before adding other data.
			$properties = $table->getProperties(1);
			$this->_cache[$pk] = Arr::toObject($properties, '\\Hubzero\\Base\\Object');

			// Convert the params field to an array.
			$registry = new Registry($table->params);
			$this->_cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.
			$path = array(
				'app'  => Filesystem::cleanPath(PATH_APP . DS . 'plugins' . DS . $table->folder . DS . $table->element . DS . $table->element . '.xml'),
				'core' => Filesystem::cleanPath(PATH_CORE . DS . 'plugins' . DS . $table->folder . DS . $table->element . DS . $table->element . '.xml')
			);

			if (file_exists($path['app']))
			{
				$this->_cache[$pk]->xml = \JFactory::getXML($path['app']);
			}
			else if (file_exists($path['core']))
			{
				$this->_cache[$pk]->xml = \JFactory::getXML($path['core']);
			}
			else
			{
				$this->_cache[$pk]->xml = null;
			}
		}

		return $this->_cache[$pk];
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 * @return  object  A database object
	*/
	public function getTable($type = 'Extension', $prefix = 'JTable', $config = array())
	{
		return \JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		// Load the User state.
		$pk = (int) Request::getInt('extension_id');
		$this->setState('plugin.id', $pk);
	}

	/**
	 * @param   object  $form   A form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @return  mixed   $group  True if successful.
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		// Initialise variables.
		$folder  = $this->getState('item.folder');
		$element = $this->getState('item.element');
		$lang    = Lang::getRoot();
		$client  = \JApplicationHelper::getClientInfo(0);

		// Load the core and/or local language sys file(s) for the ordering field.
		$db = \App::get('db');
		$query = 'SELECT element' .
				' FROM #__extensions' .
				' WHERE (type =' .$db->Quote('plugin'). 'AND folder='. $db->Quote($folder) . ')';
		$db->setQuery($query);
		$elements = $db->loadColumn();

		foreach ($elements as $elementa)
		{
			$lang->load('plg_'.$folder.'_'.$elementa.'.sys', PATH_APP, null, false, true) ||
			$lang->load('plg_'.$folder.'_'.$elementa.'.sys', PATH_APP . '/plugins/' . $folder . '/' . $elementa, null, false, true) ||
			$lang->load('plg_'.$folder.'_'.$elementa.'.sys', PATH_CORE . '/plugins/' . $folder . '/' . $elementa, null, false, true);
		}

		if (empty($folder) || empty($element))
		{
			App::redirect(Route::url('index.php?option=com_plugins', false));
		}

		// Try app: /plugins/folder/element/element.xml
		$formFile = Filesystem::cleanPath(PATH_APP . '/plugins/'.$folder.'/'.$element.'/'.$element.'.xml');
		if (!file_exists($formFile))
		{
			// Try core
			$formFile = Filesystem::cleanPath(PATH_CORE . '/plugins/'.$folder.'/'.$element.'/'.$element.'.xml');
			if (!file_exists($formFile))
			{
				throw new Exception(Lang::txt('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element.'.xml'));
				return false;
			}
		}

		// Load the core and/or local language file(s).
		$lang->load('plg_'.$folder.'_'.$element, PATH_APP, null, false, true) ||
		$lang->load('plg_'.$folder.'_'.$element, PATH_APP . '/plugins/' . $folder . '/' . $element, null, false, true) ||
		$lang->load('plg_'.$folder.'_'.$element, PATH_CORE . '/plugins/' . $folder . '/' . $element, null, false, true);

		if (file_exists($formFile))
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');
		if (!empty($help))
		{
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 * @return  array   An array of conditions to add to add to ordering queries.
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'type = '. $this->_db->quote($table->type);
		$condition[] = 'folder = '. $this->_db->quote($table->folder);
		return $condition;
	}

	/**
	 * Override method to save the form data.
	 *
	 * @param   array    $data  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		// Load the extension plugin group.
		\Plugin::import('extension');

		// Setup type
		$data['type'] = 'plugin';

		return parent::save($data);
	}

	/**
	 * Get the necessary data to load an item help screen.
	 *
	 * @return  object  An object with key, url, and local properties for loading the item help screen.
	 */
	public function getHelp()
	{
		return (object) array('key' => $this->helpKey, 'url' => $this->helpURL);
	}

	/**
	 * Custom clean cache method, plugins are cached in 2 places for different clients
	 *
	 * @param   string   $group
	 * @param   integer  $client_id
	 * @return  void
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_plugins');
	}
}
