<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Models;

use Components\Config\Models\Extension;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Hubzero\Base\Obj;
use Filesystem;
use Config;
use Notify;
use Event;
use Cache;
use Lang;
use User;
use App;

include_once __DIR__ . '/extension.php';

/**
 * Model class for Application config
 */
class Application extends Obj
{
	/**
	 * Method to get a form object.
	 *
	 * @param   array   $data  Data for the form.
	 * @return  object
	 */
	public function getForm($data = array())
	{
		$file = __DIR__ . '/forms/application.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('com_config.application', array('control' => 'hzform'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		if (!empty($data))
		{
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to get the configuration data.
	 *
	 * This method will load the global configuration data straight from
	 * Config. If configuration data has been saved in the session, that
	 * data will be merged into the original data, overwriting it.
	 *
	 * @return  array  An array containing all global config data.
	 */
	public function getData()
	{
		// Get the config data.
		$data = Config::getRoot()->toArray();

		// Set the site code, if not present
		if (isset($data['app']))
		{
			if (!isset($data['app']['sitecode']) || !$data['app']['sitecode'])
			{
				// This should be 4 alpha-numeric characters at most
				$sitename = preg_replace("/[^a-zA-Z0-9]/", '', $data['app']['sitename']);
				$data['app']['sitecode'] = strtolower(substr($sitename, 0, 4));
			}
		}

		// Prime the asset_id for the rules.
		$data['asset_id'] = 1;

		// Get the text filter data
		$params = \Component::params('com_config');
		$data['filters'] = \Hubzero\Utility\Arr::fromObject($params->get('filters'));

		// If no filter data found, get from com_content (update of 1.6/1.7 site)
		if (empty($data['filters']))
		{
			$contentParams = \Component::params('com_content');
			$data['filters'] = \Hubzero\Utility\Arr::fromObject($contentParams->get('filters'));
		}

		// Check for data in the session.
		$temp = User::getState('com_config.config.global.data');

		// Merge in the session data.
		if (!empty($temp))
		{
			$data = array_merge($data, $temp);
		}

		return $data;
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
	 * @param   array  An array containing all global config data.
	 * @return  bool   True on success, false on failure.
	 */
	public function save($data)
	{
		// Save the rules
		if (isset($data['rules']))
		{
			$rules = new \Hubzero\Access\Rules($data['rules']);

			// Check that we aren't removing our Super User permission
			// Need to get groups from database, since they might have changed
			$myGroups = \Hubzero\Access\Access::getGroupsByUser(\User::get('id'));
			$myRules = $rules->getData();
			$hasSuperAdmin = $myRules['core.admin']->allow($myGroups);
			if (!$hasSuperAdmin)
			{
				$this->setError(Lang::txt('COM_CONFIG_ERROR_REMOVING_SUPER_ADMIN'));
				return false;
			}

			$asset = \Hubzero\Access\Asset::oneByName('root.1');
			if ($asset->get('id'))
			{
				$asset->set('rules', (string) $rules);

				if (!$asset->save())
				{
					Notify::error('SOME_ERROR_CODE', $asset->getError());
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_CONFIG_ERROR_ROOT_ASSET_NOT_FOUND'));
				return false;
			}
			unset($data['rules']);
		}

		// Save the text filters
		if (isset($data['filters']))
		{
			$registry = new Registry(array('filters' => $data['filters']));

			$extension = Extension::oneByElement('com_config');

			if (!$extension->isNew())
			{
				$extension->set('params', (string) $registry);
				if (!$extension->save())
				{
					Notify::error('SOME_ERROR_CODE', $extension->getError());
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_CONFIG_ERROR_CONFIG_EXTENSION_NOT_FOUND'));
				return false;
			}
			unset($data['filters']);
		}

		// Get the previous configuration.
		$config = new \Hubzero\Config\Repository('site');

		$prev = $config->toArray();

		// We do this to preserve values that were not in the form.
		// Note: We can't use array_merge() as we're trying to preserve
		//       options that were explicitely set to blank and merging
		//       will return the previous, filled-in value
		foreach ($prev as $key => $vals)
		{
			$values = isset($data[$key]) ? $data[$key] : array();

			foreach ($vals as $k => $v)
			{
				if (!isset($values[$k]))
				{
					// Database password isn't apart of the config form
					// and we don't want to overwrite it. So we need to
					// inherit from previous settings.
					if ($key == 'database' && $k == 'password')
					{
						$values[$k] = $v;
						continue;
					}

					if (is_numeric($v))
					{
						$values[$k] = 0;
					}
					elseif (is_array($v))
					{
						$values[$k] = array();
					}
					else
					{
						$values[$k] = '';
					}
				}
			}
			ksort($values);

			$data[$key] = $values;
		}
		ksort($data);

		// Perform miscellaneous options based on configuration settings/changes.
		// Escape the offline message if present.
		if (isset($data['offline']['offline_message']))
		{
			$data['offline']['offline_message'] = \Hubzero\Utility\Str::ampReplace($data['offline']['offline_message']);
		}

		// Purge the database session table if we are changing to the database handler.
		if ($prev['session']['session_handler'] != 'database'
		 && $data['session']['session_handler'] == 'database')
		{
			$db = App::get('db');

			$past = time() + 1;
			$query = $db->getQuery()
				->delete('#__sessions')
				->where('time', '<', (int) $past);

			$db->setQuery($query->toString());
			$db->execute();
		}

		if (empty($data['cache']['cache_handler']))
		{
			$data['cache']['caching'] = 0;
		}

		// Clean the cache if disabled but previously enabled.
		if ((!$data['cache']['caching'] && $prev['cache']['caching'])
		 || $data['cache']['cache_handler'] !== $prev['cache']['cache_handler'])
		{
			try
			{
				Cache::clean();
			}
			catch (\Exception $e)
			{
				Notify::error('SOME_ERROR_CODE', $e->getMessage());
			}
		}

		// Overwrite the old FTP credentials with the new ones.
		if (isset($data['ftp']))
		{
			// Fix misnamed FTP key
			// Not sure how or where this originally happened...
			if (!isset($data['ftp']['ftp_enable'])
			 && isset($data['ftp']['ftp_enabled']))
			{
				$data['ftp']['ftp_enable'] = $data['ftp']['ftp_enabled'];
				unset($data['ftp']['ftp_enabled']);
			}

			$temp = Config::getRoot();
			$temp->set('ftp.ftp_enable', $data['ftp']['ftp_enable']);
			$temp->set('ftp.ftp_host', $data['ftp']['ftp_host']);
			$temp->set('ftp.ftp_port', $data['ftp']['ftp_port']);
			$temp->set('ftp.ftp_user', $data['ftp']['ftp_user']);
			$temp->set('ftp.ftp_pass', $data['ftp']['ftp_pass']);
			$temp->set('ftp.ftp_root', $data['ftp']['ftp_root']);
		}

		// Clear cache of com_config component.
		Cache::clean('_system');

		$result = Event::trigger('onApplicationBeforeSave', array($data));

		// Store the data.
		if (in_array(false, $result, true))
		{
			throw new \RuntimeException(Lang::txt('COM_CONFIG_ERROR_UNKNOWN_BEFORE_SAVING'));
		}

		// Write the configuration file.
		$return = $this->writeConfigFile($data);

		// Trigger the after save event.
		Event::trigger('onApplicationAfterSave', array($data));

		return $result;
	}

	/**
	 * Method to unset the root_user value from configuration data.
	 *
	 * This method will load the global configuration data straight from
	 * Config and remove the root_user value for security, then save the configuration.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	public function removeroot()
	{
		// Get the previous configuration.
		$prev = Config::getRoot()->toArray();

		// Create the new configuration object, and unset the root_user property
		unset($prev['root_user']);

		$config = new Registry($prev);

		// Write the configuration file.
		return $this->writeConfigFile($config);
	}

	/**
	 * Method to write the configuration to a file.
	 *
	 * @param   object  $config  A Registry object containing all global config data.
	 * @return  bool    True on success, false on failure.
	 * @since   2.5.4
	 */
	private function writeConfigFile($data)
	{
		if ($data instanceof \Hubzero\Config\Repository)
		{
			$data = $data->toArray();
		}

		// Attempt to write the configuration files
		$writer = new \Hubzero\Config\FileWriter(
			'php',
			PATH_APP . DS . 'config'
		);

		$client = null;

		foreach ($data as $group => $values)
		{
			if (!$writer->write($values, $group, $client))
			{
				$this->setError(Lang::txt('COM_CONFIG_ERROR_WRITE_FAILED'));
				return false;
			}
		}

		$legacy = new \Hubzero\Config\Legacy();
		if ($legacy->exists())
		{
			$legacy->reset();

			foreach ($data as $group => $values)
			{
				foreach ($values as $key => $val)
				{
					$legacy->set($key, $val);
				}
			}

			$legacy->update();
		}

		return true;
	}
}
