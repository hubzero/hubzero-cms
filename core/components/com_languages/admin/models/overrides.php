<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

jimport('joomla.application.component.modellist');

/**
 * Languages Overrides Model
 *
 * @package			Joomla.Administrator
 * @subpackage	com_languages
 * @since				2.5
 */
class LanguagesModelOverrides extends JModelList
{
	/**
	 * Constructor
	 *
	 * @param		array	An optional associative array of configuration settings
	 *
	 * @return	void
	 *
	 * @since		2.5
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->filter_fields = array('key', 'text');
	}

	/**
	 * Retrieves the overrides data
	 *
	 * @param		boolean	True if all overrides shall be returned without considering pagination, defaults to false
	 *
	 * @return	array		Array of objects containing the overrides of the override.ini file
	 *
	 * @since		2.5
	 */
	public function getOverrides($all = false)
	{
		// Get a storage key
		$store = $this->getStoreId();

		// Try to load the data from internal storage
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Parse the override.ini file in oder to get the keys and strings
		$filename = PATH_APP . DS . 'bootstrap' . DS . $this->getState('filter.client') .DS.'language'.DS.'overrides'.DS.$this->getState('filter.language').'.override.ini';
		$strings = LanguagesHelper::parseFile($filename);

		// Consider the odering
		if ($this->getState('list.ordering') == 'text')
		{
			if (strtoupper($this->getState('list.direction')) == 'DESC')
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
			if (strtoupper($this->getState('list.direction')) == 'DESC')
			{
				krsort($strings);
			}
			else
			{
				ksort($strings);
			}
		}

		// search strings
		if ($search = Request::getVar('filter_search', null))
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
		if (!$all && $this->getState('list.limit') && $this->getTotal() > $this->getState('list.limit'))
		{
			$strings = array_slice($strings, $this->getStart(), $this->getState('list.limit'), true);
		}

		// Add the items to the internal cache
		$this->cache[$store] = $strings;

		return $this->cache[$store];
	}

	/**
	 * Method to get the total number of overrides
	 *
	 * @return	int	The total number of overrides
	 *
	 * @since		2.5
	 */
	public function getTotal()
	{
		// Get a storage key
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Add the total to the internal cache
		$this->cache[$store] = count($this->getOverrides(true));

		return $this->cache[$store];
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param		string	An optional ordering field.
	 * @param		string	An optional direction (asc|desc).
	 *
	 * @return	void
	 *
	 * @since		2.5
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Use default language of frontend for default filter
		$default = Component::params('com_languages')->get('site', 'en-GB').'0';

		$old_language_client = User::getState('com_languages.overrides.filter.language_client', '');
		$language_client     = $this->getUserStateFromRequest('com_languages.overrides.filter.language_client', 'filter_language_client', $default, 'cmd');

		if ($old_language_client != $language_client)
		{
			$client   = substr($language_client, -1);
			$language = substr($language_client, 0, -1);
		}
		else
		{
			$client   = User::getState('com_languages.overrides.filter.client', 0);
			$language = User::getState('com_languages.overrides.filter.language', 'en-GB');
		}

		$this->setState('filter.language_client', $language.$client);
		$this->setState('filter.client', $client ? 'administrator' : 'site');
		$this->setState('filter.language', $language);
		$this->setState('filter.search', Request::getVar('filter_search', null));

		// Add filters to the session because they won't be stored there
		// by 'getUserStateFromRequest' if they aren't in the current request
		User::setState('com_languages.overrides.filter.client', $client);
		User::setState('com_languages.overrides.filter.language', $language);

		// List state information
		parent::populateState('key', 'asc');
	}

	/**
	 * Method to get all found languages of frontend and backend.
	 *
	 * The resulting array has entries of the following style:
	 * <Language Tag>0|1 => <Language Name> - <Client Name>
	 *
	 * @return	array	Sorted associative array of languages
	 *
	 * @since		2.5
	 */
	public function getLanguages()
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

	/**
	 * Method to delete one or more overrides
	 *
	 * @param		array		Array of keys to delete
	 *
	 * @return	int			Number of successfully deleted overrides, boolean false if an error occured
	 *
	 * @since		2.5
	 */
	public function delete($cids)
	{
		// Check permissions first
		if (!User::authorise('core.delete', 'com_languages'))
		{
			$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		require_once PATH_COMPONENT.'/helpers/languages.php';

		// Parse the override.ini file in oder to get the keys and strings
		//$filename = constant('JPATH_'.strtoupper($this->getState('filter.client'))).DS.'language'.DS.'overrides'.DS.$this->getState('filter.language').'.override.ini';
		$filename = PATH_APP . DS . 'bootstrap' . DS . $this->getState('filter.client').DS.'language'.DS.'overrides'.DS.$this->getState('filter.language').'.override.ini';
		$strings = LanguagesHelper::parseFile($filename);

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

		//$filename = constant('JPATH_'.strtoupper($this->getState('filter.client'))).DS.'language'.DS.'overrides'.DS.$this->getState('filter.language').'.override.ini';
		$filename = PATH_APP . DS . 'bootstrap' . DS . $this->getState('filter.client').DS.'language'.DS.'overrides'.DS.$this->getState('filter.language').'.override.ini';

		if (!Filesystem::write($filename, $registry->toString('INI')))
		{
			return false;
		}

		$this->cleanCache();

		return count($cids);
	}
}
