<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Members\Models\Import;

use Exception;
use stdClass;
use JText;
use JFactory;
use JRoute;
use JURI;
use JParameter;

include_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php';
include_once JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'tags.php';
include_once JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'registration.php';

/**
 * Member Record importer
 */
class Record extends \Hubzero\Content\Import\Model\Record
{
	/**
	 * Profile
	 *
	 * @var  object
	 */
	private $_profile;

	/**
	 *  Constructor
	 *
	 * @param   mixes   $raw      Raw data
	 * @param   array   $options  Import options
	 * @param   string  $mode     Operation mode (update|patch)
	 * @return  void
	 */
	public function __construct($raw, $options = array(), $mode = 'UPDATE')
	{
		// store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = $mode;

		// Core objects
		$this->_database = JFactory::getDBO();
		$this->_user     = JFactory::getUser();

		// Create objects
		$this->record = new stdClass;
		$this->record->entry = new \MembersProfile($this->_database);
		$this->_profile = new \Hubzero\User\Profile();
		$this->record->tags  = array();

		// Messages
		$this->record->errors  = array();
		$this->record->notices = array();

		// bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return  $this  Current object
	 */
	public function bind()
	{
		// Wrap in try catch to avoid breaking in middle of import
		try
		{
			// Map profile data
			$this->_mapEntryData();

			// Map tags
			$this->_mapTagsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return  $this  Current object
	 */
	public function check()
	{
		// Run save check method
		if (!$this->record->entry->check())
		{
			array_push($this->record->errors, $this->record->entry->getError());
			return $this;
		}

		$xregistration = new \MembersModelRegistration();
		$xregistration->loadProfile($this->_profile);

		// Check that required fields were filled in properly
		if (!$xregistration->check('edit', $this->_profile->get('uidNumber'), array()))
		{
			if (!empty($xregistration->_missing))
			{
				foreach ($xregistration->_missing as $missing)
				{
					array_push($this->record->errors, $missing);
				}
			}
			if (!empty($xregistration->_invalid))
			{
				foreach ($xregistration->_invalid as $invalid)
				{
					array_push($this->record->errors, $invalid);
				}
			}
			//array_push($this->record->errors, $this->record->entry->getError());
		}

		return $this;
	}

	/**
	 * Store data
	 *
	 * @param   integer  $dryRun  Dry Run mode
	 * @return  $this    Current object
	 */
	public function store($dryRun = 1)
	{
		// Are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			return $this;
		}

		// Attempt to save all data
		// Wrap in try catch to avoid break mid import
		try
		{
			// Save profile
			$this->_saveEntryData();

			// Save tags
			$this->_saveTagsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		return $this;
	}

	/**
	 * Map raw data to profile object
	 *
	 * @return  void
	 */
	private function _mapEntryData()
	{
		// do we want to do a title match?
		/*if ($this->_options['namematch'] == 1 && isset($this->record->type->id))
		{
			$sql = 'SELECT id, name, LEVENSHTEIN(name, ' . $this->_database->quote($this->raw->name) . ' ) as nameDiff
			        FROM `#__users`
			        HAVING nameDiff < ' . self::TITLE_MATCH;
			$this->_database->setQuery($sql);
			$results = $this->_database->loadObjectList('id');

			// did we get more then one result?
			if (count($results) > 1)
			{
				$ids = implode(", ", array_keys($results));
				throw new Exception(JText::sprintf('Unable to determine which member to overwrite. The following membera have similar names: %s', $ids));
			}

			// if we only have one were all good
			if (count($results) == 1)
			{
				// set our id to the matched resource
				$entry = reset($results);
				$this->raw->id = $entry->id;

				// add a notice with link to resource matched
				$resourceLink = rtrim(str_replace('administrator', '', JURI::base()), DS) . DS . 'members' . DS . $entry->id;
				$link = '<a target="_blank" href="' . $resourceLink . '">' . $resourceLink . '</a>';
				array_push($this->record->notices, JText::sprintf('COM_MEMBERS_IMPORT_RECORD_MODEL_MATCHEDBYNAME', $link));
			}
		}*/

		// Do we have an ID?
		// Either passed in the raw data or gotten from the title match
		if (isset($this->raw->uidNumber) && $this->raw->uidNumber > 1)
		{
			$this->record->entry->load($this->raw->uidNumber);
		}
		else if (isset($this->raw->username) && $this->raw->username)
		{
			$this->record->entry->loadByUsername($this->raw->username);
		}

		if (!$this->record->entry->get('uidNumber'))
		{
			$this->raw->registerDate = JFactory::getDate()->toSql();
		}

		// set modified date/user
		$this->raw->modifiedDate = JFactory::getDate()->toSql();

		/*if (isset($this->_options['emailConfirmed']))
		{
			$this->raw->emailConfirmed = (int) $this->_options['emailConfirmed'];
		}

		if (isset($this->_options['public']))
		{
			$this->raw->public = (int) $this->_options['public'];
		}

		if (isset($this->_options['mailPreferenceOption']))
		{
			$this->raw->mailPreferenceOption = (int) $this->_options['mailPreferenceOption'];
		}*/

		/*$this->record->entry->bind($this->raw);*/

		foreach (get_object_vars($this->raw) as $key => $val)
		{
			// These two need some extra loving and care, so we skip them for now...
			if (substr($key, 0, 1) == '_' || $key == 'username' || $key == 'uidNumber')
			{
				continue;
			}

			$this->record->entry->set($key, $val);
		}

		if (isset($this->raw->disability))
		{
			$disability = $this->raw->disability;

			if (is_string($disability))
			{
				$disability = array_map('trim', preg_split("/(,|;)/", $disability));
				$disability = array_values(array_filter($disability));
			}

			$this->record->entry->set('disability', $disability);
		}

		if (isset($this->raw->race))
		{
			$race = $this->raw->race;

			if (is_string($race))
			{
				$race = array_map('trim', preg_split("/(,|;)/", $race));
				$race = array_values(array_filter($race));
			}

			$this->record->entry->set('race', $race);
		}

		// If we have a name but no individual parts...
		if (!$this->record->entry->get('givenName') && !$this->record->entry->get('surame') && $this->record->entry->get('name'))
		{
			$name = explode(' ', $this->record->entry->get('name'));
			$this->record->entry->set('givenName',  array_shift($name));
			$this->record->entry->set('surname',    array_pop($name));
			$this->record->entry->set('middleName', implode(' ', $name));
		}

		// If we have the individual name parts but not the combined whole...
		if (($this->record->entry->get('givenName') || $this->record->entry->get('surame')) && !$this->record->entry->get('name'))
		{
			$name = array(
				$this->record->entry->get('givenName'),
				$this->record->entry->get('middleName'),
				$this->record->entry->get('surname')
			);
			$this->record->entry->set('name', implode(' ', $name));
		}

		// If we're updating an existing record...
		if ($this->record->entry->get('uidNumber'))
		{
			// Check if the username passed if the same for the record we're updating
			$username = $this->record->entry->get('username');
			if ($username && $username != $this->raw->username)
			{
				// Uh-oh. Notify the user.
				array_push($this->record->notices, JText::_('Usernames for existing members cannot be changed at this time.'));
			}
		}
		else if (isset($this->raw->username) && $this->raw->username)
		{
			$this->record->entry->set('username', $this->raw->username);
		}

		// Bind to the profile object
		foreach ($this->record->entry->getProperties() as $key => $val)
		{
			$this->_profile->set($key, $val);
		}
	}

	/**
	 * Save profile
	 *
	 * @return  void
	 */
	private function _saveEntryData()
	{
		if (!$this->_profile->store())
		{
			throw new Exception(JText::_('Unable to save the entry data.'));
		}

		if ($password = $this->raw->password)
		{
			\Hubzero\User\Password::changePassword($this->_profile->get('username'), $password);
		}
	}

	/**
	 * Map Tags
	 *
	 * @return  void
	 */
	private function _mapTagsData()
	{
		if (isset($this->raw->tags))
		{
			$tags = $this->raw->tags;

			if (is_string($tags))
			{
				$tags = array_map('trim', preg_split("/(,|;)/", $tags));
				$tags = array_values(array_filter($tags));
			}

			$this->record->tags = $tags;
		}
	}

	/**
	 * Save tags
	 *
	 * @return  void
	 */
	private function _saveTagsData()
	{
		// save tags
		$tags = new \MembersModelsTags($this->record->entry->uidNumber);
		$tags->setTags($this->record->tags, $this->_user->get('id'));
	}
}