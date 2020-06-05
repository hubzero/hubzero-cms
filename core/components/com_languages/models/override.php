<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Models;

use Hubzero\Base\Obj;
use Filesystem;
use Lang;

/**
 * Language override model
 */
class Override extends Obj
{
	/**
	 * Cache
	 *
	 * @var  array
	 */
	protected $cache = array();

	/**
	 * Path to overrides
	 *
	 * @param   string  $client
	 * @param   string  $language
	 * @return  string
	 */
	public function path($client, $language)
	{
		return PATH_APP . DS . 'bootstrap' . DS . $client . DS . 'language' . DS . 'overrides' . DS . $language . '.override.ini';
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   string  $pk  The key name.
	 * @return  object
	 */
	public function one($pk = null)
	{
		$filename = $this->path($this->get('client'), $this->get('language'));

		$strings = self::parseFile($filename);

		$result = new \stdClass();
		$result->key = '';
		$result->override = '';
		$result->language = $this->get('language');
		$result->client   = $this->get('client');
		$result->file     = $filename;

		if (isset($strings[$pk]))
		{
			$result->key = $pk;
			$result->override = $strings[$pk];
		}

		return $result;
	}

	/**
	 * Retrieves the overrides data
	 *
	 * @param   boolean  $all  True if all overrides shall be returned without considering pagination, defaults to false
	 * @return  array    Array of objects containing the overrides of the override.ini file
	 */
	public function all($all = false)
	{
		// Get a storage key
		$store = $this->getStoreId();

		// Try to load the data from internal storage
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Parse the override.ini file in oder to get the keys and strings
		$filename = $this->path($this->get('client'), $this->get('language'));

		$strings = self::parseFile($filename);

		// Consider the odering
		if ($this->get('sort') == 'text')
		{
			if (strtoupper($this->get('sort_Dir')) == 'DESC')
			{
				arsort($strings);
			}
			else
			{
				asort($strings);
			}
		}
		else
		{
			if (strtoupper($this->get('sort_Dir')) == 'DESC')
			{
				krsort($strings);
			}
			else
			{
				ksort($strings);
			}
		}

		// search strings
		if ($search = $this->get('search'))
		{
			// run callback on each string
			array_walk($strings, function($value, $key) use (&$strings, $search)
			{
				// remove if we dont fine a case insensitive match in either key or value
				if (!preg_match("/{$search}/ui", $key) && !preg_match("/{$search}/ui", $value))
				{
					unset($strings[$key]);
				}
			});
		}

		// Consider the pagination
		if (!$all && $this->get('limit') && $this->total() > $this->get('limit'))
		{
			$strings = array_slice($strings, $this->get('start'), $this->get('limit'), true);
		}

		// Add the items to the internal cache
		$this->cache[$store] = $strings;

		return $this->cache[$store];
	}

	/**
	 * Method to get the total number of overrides
	 *
	 * @return  integer  The total number of overrides
	 */
	public function total()
	{
		// Get a storage key
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Add the total to the internal cache
		$this->cache[$store] = count($this->all(true));

		return $this->cache[$store];
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->get('start');
		$id .= ':' . $this->get('limit');
		$id .= ':' . $this->get('sort');
		$id .= ':' . $this->get('sort_Dir');

		return md5('overrides:' . $id);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array    $data             The form data.
	 * @param   boolean  $opposite_client  Indicates whether the override should not be created for the current client
	 * @return  boolean  True on success, false otherwise.
	 */
	public function save($data, $opposite_client = false)
	{
		$client   = $this->get('client', 0);
		$language = $this->get('language', 'en-GB');

		// If using client name
		if (!is_numeric($client))
		{
			// Get the numeric client value
			$client = \Hubzero\Base\ClientManager::client($client, true)->id;
		}

		// If the override should be created for both
		if ($opposite_client)
		{
			$client = 1 - $client;
		}

		$client = \Hubzero\Base\ClientManager::client($client)->name;

		// Parse the override.ini file in oder to get the keys and strings
		$filename = $this->path($client, $language);

		$strings = self::parseFile($filename);

		if (isset($strings[$data['id']]))
		{
			// If an existent string was edited check whether
			// the name of the constant is still the same
			if ($data['key'] == $data['id'])
			{
				// If yes, simply override it
				$strings[$data['key']] = $data['override'];
			}
			else
			{
				// If no, delete the old string and prepend the new one
				unset($strings[$data['id']]);
				$strings = array($data['key'] => $data['override']) + $strings;
			}
		}
		else
		{
			// If it is a new override simply prepend it
			$strings = array($data['key'] => $data['override']) + $strings;
		}

		foreach ($strings as $key => $string)
		{
			$strings[$key] = str_replace('"', '"_QQ_"', $string);
		}

		// Write override.ini file with the strings
		$registry = new \Hubzero\Config\Registry($strings);

		if (!Filesystem::write($filename, $registry->toString('INI')))
		{
			return false;
		}

		// If the override should be stored for both clients save
		// it also for the other one and prevent endless recursion
		if (isset($data['both']) && $data['both'] && !$opposite_client)
		{
			return $this->save($data, true);
		}

		return true;
	}

	/**
	 * Method to delete one or more overrides
	 *
	 * @param   array    $cids  Array of keys to delete
	 * @return  integer  Number of successfully deleted overrides, boolean false if an error occured
	 */
	public function delete($cids)
	{
		// Parse the override.ini file in oder to get the keys and strings
		$filename = $this->path($this->get('client'), $this->get('language'));

		$strings = self::parseFile($filename);

		// Unset strings that shall be deleted
		foreach ($cids as $key)
		{
			if (isset($strings[$key]))
			{
				unset($strings[$key]);
			}
		}

		foreach ($strings as $key => $string)
		{
			$strings[$key] = str_replace('"', '"_QQ_"', $string);
		}

		// Write override.ini file with the left strings
		$registry = new \Hubzero\Config\Registry($strings);

		if (!Filesystem::write($filename, $registry->toString('INI')))
		{
			return false;
		}

		\Cache::clean();

		return count($cids);
	}

	/**
	 * Method for parsing ini files
	 *
	 * @param   string  $filename  Path and name of the ini file to parse
	 * @return  array   Array of strings found in the file, the array indices will be the keys. On failure an empty array will be returned
	 */
	public static function parseFile($filename)
	{
		if (!Filesystem::exists($filename))
		{
			return array();
		}

		// Capture hidden PHP errors from the parsing
		$version      = phpversion();
		$php_errormsg = null;
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);

		if ($version >= '5.3.1')
		{
			$contents = file_get_contents($filename);
			$contents = str_replace('_QQ_', '"\""', $contents);
			$strings  = @parse_ini_string($contents);

			if ($strings === false)
			{
				return array();
			}
		}
		else
		{
			$strings = @parse_ini_file($filename);

			if ($strings === false)
			{
				return array();
			}

			if ($version == '5.3.0' && is_array($strings))
			{
				foreach ($strings as $key => $string)
				{
					$strings[$key] = str_replace('_QQ_', '"', $string);
				}
			}
		}

		return $strings;
	}

	/**
	 * Method to get all found languages of frontend and backend.
	 *
	 * The resulting array has entries of the following style:
	 * <Language Tag>0|1 => <Language Name> - <Client Name>
	 *
	 * @return  array  Sorted associative array of languages
	 */
	public function languages()
	{
		// Try to load the data from internal storage
		if (!empty($this->cache['languages']))
		{
			return $this->cache['languages'];
		}

		// Get all languages of frontend and backend
		$languages = array();

		$site_languages  = Lang::getKnownLanguages(PATH_CORE . DS . 'bootstrap' . DS . 'Site');
		$admin_languages = Lang::getKnownLanguages(PATH_CORE . DS . 'bootstrap' . DS . 'Administrator');
		foreach ($site_languages as $tag => $language)
		{
			$languages[$tag.'0'] = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_LANGUAGES_BOX_ITEM', $language['name'], Lang::txt('JSITE'));
		}
		foreach ($admin_languages as $tag => $language)
		{
			$languages[$tag.'1'] = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_LANGUAGES_BOX_ITEM', $language['name'], Lang::txt('JADMINISTRATOR'));
		}

		// Overwrite core languages with any installed ones
		$site_languages  = Lang::getKnownLanguages(PATH_APP . DS . 'bootstrap' . DS . 'site');
		$admin_languages = Lang::getKnownLanguages(PATH_APP . DS . 'bootstrap' . DS . 'administrator');

		// Create a single array of them
		foreach ($site_languages as $tag => $language)
		{
			$languages[$tag.'0'] = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_LANGUAGES_BOX_ITEM', $language['name'], Lang::txt('JSITE'));
		}
		foreach ($admin_languages as $tag => $language)
		{
			$languages[$tag.'1'] = Lang::txt('COM_LANGUAGES_VIEW_OVERRIDES_LANGUAGES_BOX_ITEM', $language['name'], Lang::txt('JADMINISTRATOR'));
		}

		// Sort it by language tag and by client after that
		ksort($languages);

		// Add the languages to the internal cache
		$this->cache['languages'] = $languages;

		return $this->cache['languages'];
	}
}
