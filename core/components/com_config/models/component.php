<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Models;

use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Hubzero\Base\Obj;
use Exception;
use Filesystem;
use Request;
use Event;
use Cache;
use Lang;

include_once __DIR__ . '/extension.php';

/**
 * Model class for Component config
 */
class Component extends Obj
{
	/**
	 * The event to trigger before saving the data.
	 *
	 * @var  string
	 */
	protected $event_before_save = 'onConfigurationBeforeSave';

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var  string
	 */
	protected $event_after_save = 'onConfigurationAfterSave';

	/**
	 * Class constructor, overridden in descendant classes.
	 *
	 * @param   mixed  $properties  Either and associative array or another object to set the initial properties of the object.
	 * @return  void
	 */
	public function __construct($properties = null)
	{
		parent::__construct($properties);

		$this->populateState();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling get in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		// Set the component (option) we are dealing with.
		$component = Request::getCmd('component');
		$this->set('component.option', $component);

		// Set an alternative path for the configuration file.
		if ($path = Request::getString('path'))
		{
			$path = Filesystem::cleanPath($path);
			\Hubzero\Filesystem\Util::checkPath(PATH_ROOT . DS . $path);
			$this->set('component.path', $path);
		}
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @return  mixed    A Form object on success, false on failure
	 */
	public function getForm($data = array())
	{
		$file = \Component::path($this->get('component.option')) . '/config/config.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(\Component::path($this->get('component.option')) . '/models/fields');

		$form = new Form('com_config.component', array('control' => 'hzform'));

		if (!$form->loadFile($file, false, '//config'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
			return false;
		}

		try
		{
			// Trigger the form preparation event.
			$results = Event::trigger('onContentPrepareForm', array($form, $data));

			// Check for errors encountered while preparing the form.
			if (count($results) && in_array(false, $results, true))
			{
				// Get the last error.
				$error = Event::getError();

				if (!($error instanceof Exception))
				{
					throw new Exception($error);
				}
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		if (!empty($data))
		{
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Get the component information.
	 *
	 * @return  object
	 */
	public function getComponent()
	{
		// Initialise variables.
		$option = $this->get('component.option');

		// Load common and local language files.
		Lang::load($option, PATH_APP . '/bootstrap/administrator', null, false, true)
		|| Lang::load($option, \Component::path($option) . '/admin', null, false, true)
		|| Lang::load($option, \Component::path($option) . '/site', null, false, true);

		if ($path = $this->get('component.path'))
		{
			Lang::load($option, PATH_ROOT . dirname($path) . DS . 'Admin', null, false, true);
			Lang::load($option, PATH_ROOT . dirname($path) . DS . 'Site', null, false, true);
		}

		return \Component::load($option);
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   object  $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 * @return  mixed   Array of filtered data if valid, false otherwise.
	 */
	public function validate($form, $data, $group = null)
	{
		// Filter and validate the form data.
		$data = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof \Exception)
		{
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError(Lang::txt($message));
			}

			return false;
		}

		return $data;
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @param   array  $data  An array containing all global config data.
	 * @return  bool   True on success, false on failure.
	 */
	public function save($data)
	{
		$isNew = true;

		// Save the rules.
		if (isset($data['params']) && isset($data['params']['rules']))
		{
			$rules = new \Hubzero\Access\Rules($data['params']['rules']);
			$asset = \Hubzero\Access\Asset::oneByName($data['option']);

			if ($asset->isNew())
			{
				$root = \Hubzero\Access\Asset::oneByName('root.1');
				$asset->set('name', $data['option']);
				$asset->set('title', $data['option']);
				//$asset->setLocation($root->get('id'), 'last-child');
				$asset->saveAsLastChildOf($root);
			}
			$asset->set('rules', (string) $rules);

			if (!$asset->save())
			{
				$this->setError($asset->getError());
				return false;
			}

			// We don't need this anymore
			unset($data['params']['rules']);
		}

		if (isset($data['option']))
		{
			unset($data['option']);
		}

		$table = Extension::oneOrFail($data['id']);

		// Load the previous Data
		if ($table->isNew())
		{
			$this->setError($table->getError());
			return false;
		}

		unset($data['id']);

		// Bind the data.
		$table->set($data);

		// Trigger the onConfigurationBeforeSave event.
		$result = Event::trigger($this->event_before_save, array('com_config.component', $table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->save())
		{
			$this->setError($table->getError());
			return false;
		}

		// Clean the component cache.
		Cache::clean('_system');

		// Trigger the onConfigurationAfterSave event.
		Event::trigger($this->event_after_save, array('com_config.component', $table, $isNew));

		return true;
	}
}
