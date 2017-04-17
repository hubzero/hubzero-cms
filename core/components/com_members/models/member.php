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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Components\Members\Models;
use Hubzero\User\User;
use Hubzero\Config\Registry;
require_once(__DIR__ . DS . 'profile.php');
require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'note.php');
require_once(__DIR__ . DS . 'quota.php');
require_once(__DIR__ . DS . 'host.php');

/**
 * User model
 */
class Member extends User
{
	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__users';
	/**
	 * Has profile data been loaded?
	 *
	 * @var  bool
	 */
	private $profileLoaded = false;
	/**
	 * Get profile fields
	 *
	 * @return  object
	 */
	public function profiles()
	{
		return $this->oneToMany('Profile', 'user_id');
	}
	/**
	 * Get notes
	 *
	 * @return  object
	 */
	public function notes()
	{
		return $this->oneToMany('Note', 'user_id');
	}
	/**
	 * Get quota
	 *
	 * @return  object
	 */
	public function quota()
	{
		return $this->oneToOne('Quota', 'user_id');
	}
	/**
	 * Get hosts
	 *
	 * @return  object
	 */
	public function hosts()
	{
		return $this->oneToMany('Host', 'uidNumber');
	}
	/**
	 * Gets an attribute by key
	 *
	 * This will not retrieve properties directly attached to the model,
	 * even if they are public - those should be accessed directly!
	 *
	 * Also, make sure to access properties in transformers using the get method.
	 * Otherwise you'll just get stuck in a loop!
	 *
	 * @param   string  $key      The attribute key to get
	 * @param   mixed   $default  The value to provide, should the key be non-existent
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		if ($key == 'tags')
		{
			return $this->tags();
		}
		if (!$this->hasAttribute($key) && !$this->profileLoaded)
		{
			// Collect multi-value fields into arrays
			$data = Profile::collect($this->profiles);
			foreach ($data as $k => $v)
			{
				$this->set($k, $v);
			}
			$this->profileLoaded = true;
		}
		return parent::get($key, $default);
	}
	/**
	 * Is the user's email confirmed?
	 *
	 * @return  boolean
	 */
	public function isEmailConfirmed()
	{
		return ($this->get('emailConfirmed') == 1);
	}
	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		if (!$this->get('id'))
		{
			return '';
		}
		$link = 'index.php?option=com_members&id=' . $this->get('id');
		// If it doesn't exist or isn't published
		$type = strtolower($type);
		switch ($type)
		{
			case 'edit':
			case 'changepassword':
				$link .= '&task=' . $type;
			break;
			default:
			break;
		}
		return $link;
	}
	/**
	 * Get tags on an entry
	 *
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($what))
			{
				case 'array':
					return array();
				break;
				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}
		$cloud = new Tags($this->get('id'));
		return $cloud->render($what, array('admin' => $admin));
	}
	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));
		return $cloud->setTags($tags, $user_id, $admin);
	}
	/**
	 * Save data
	 *
	 * @return  boolean
	 */
	public function save()
	{
		if (is_array($this->get('params')))
		{
			$params = new Registry($this->get('params'));
			$this->set('params', $params);
		}
		if (is_object($this->get('params')))
		{
			$this->set('params', $this->get('params')->toString());
		}
		// Map set data to profile fields
		$attribs = $this->getAttributes();
		$columns = $this->getStructure()->getTableColumns($this->getTableName());
		$profile = null;
		foreach ($attribs as $key => $val)
		{
			if ($key == 'accessgroups')
			{
				continue;
			}
			if ($key == 'profile' || $key == 'profiles')
			{
				$profile = $val;
			}
			if (!isset($columns[$key]))
			{
				$this->removeAttribute($key);
			}
		}
		// Save record
		$result = parent::save();
		if ($result)
		{
			if ($profile)
			{
				$result = $this->saveProfile($profile);
			}
		}
		if (!$result)
		{
			// Reset the data to the way it was before save attempt
			$this->set($attribs);
		}
		return $result;
	}
	/**
	 * Save profile data
	 *
	 * @param   array   $profile
	 * @param   array   $access
	 * @return  boolean
	 */
	public function saveProfile($profile, $access = array())
	{
		$profile = (array)$profile;
		$access  = (array)$access;
		$keep = array();
		foreach ($this->profiles as $field)
		{
			// Remove any entries not in the incoming data
			if (!isset($profile[$field->get('profile_key')]))
			{
				if (!$field->destroy())
				{
					$this->addError($field->getError());
					return false;
				}
				continue;
			}
			// Push to the list of fields we want to keep
			if (!isset($keep[$field->get('profile_key')]))
			{
				$keep[$field->get('profile_key')] = $field;
			}
			else
			{
				// Multi-value field
				$values = $keep[$field->get('profile_key')];
				$values = is_array($values) ? $values : array($values->get('profile_value') => $values);
				$values[$field->get('profile_value')] = $field;
				$keep[$field->get('profile_key')] = $values;
			}
		}
		$i = 1;
		foreach ($profile as $key => $data)
		{
			if ($key == 'tag' || $key == 'tags')
			{
				$this->tag($data);
				continue;
			}
			// Is it a multi-value field?
			if (is_array($data))
			{
				if (empty($data))
				{
					continue;
				}
				foreach ($data as $val)
				{
					if (is_array($val) || is_object($val))
					{
						$val = json_encode($val);
					}
					$val = trim($val);
					// Skip empty values
					if (!$val)
					{
						continue;
					}
					$field = null;
					// Try to find an existing entry
					if (isset($keep[$key]))
					{
						if (is_array($keep[$key]))
						{
							if (isset($keep[$key][$val]))
							{
								$field = $keep[$key][$val];
								unset($keep[$key][$val]);
							}
						}
						else
						{
							$field = $keep[$key];
							unset($keep[$key]);
						}
					}
					if (!($field instanceof Profile))
					{
						$field = Profile::blank();
					}
					$field->set(array(
						'user_id'       => $this->get('id'),
						'profile_key'   => $key,
						'profile_value' => $val,
						'ordering'      => $i,
						'access'        => (isset($access[$key]) ? $access[$key] : $field->get('access', 5))
					));
					if (!$field->save())
					{
						$this->addError($field->getError());
						return false;
					}
				}
				// Remove any values not already found
				if (isset($keep[$key]) && is_array($keep[$key]))
				{
					foreach ($keep[$key] as $f)
					{
						if (!$f->destroy())
						{
							$this->addError($f->getError());
							return false;
						}
					}
				}
			}
			else
			{
				$val = trim($data);
				$field = null;
				if (isset($keep[$key]))
				{
					$field = $keep[$key];
				}
				if (!($field instanceof Profile))
				{
					$field = Profile::blank();
				}
				// If value is empty
				if (!$val)
				{
					// If an existing field, remove it
					if ($field->get('id'))
					{
						if (!$field->destroy())
						{
							$this->addError($field->getError());
							return false;
						}
					}
					// Move along. Nothing to see here.
					continue;
				}
				$field->set(array(
					'user_id'       => $this->get('id'),
					'profile_key'   => $key,
					'profile_value' => $val,
					'ordering'      => $i,
					'access'        => (isset($access[$key]) ? $access[$key] : $field->get('access', 5))
				));
				if (!$field->save())
				{
					$this->addError($field->getError());
					return false;
				}
			}
			$i++;
		}
		return true;
	}
	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		$data = $this->toArray();
		Event::trigger('user.onUserBeforeDelete', array($data));
		// Remove profile fields
		foreach ($this->profiles()->rows() as $field)
		{
			if (!$field->destroy())
			{
				$this->addError($field->getError());
				return false;
			}
		}
		// Remove notes
		foreach ($this->notes()->rows() as $note)
		{
			if (!$note->destroy())
			{
				$this->addError($note->getError());
				return false;
			}
		}
		// Remove hosts
		foreach ($this->hosts()->rows() as $host)
		{
			if (!$host->destroy())
			{
				$this->addError($host->getError());
				return false;
			}
		}
		// Remove tags
		$this->tag('');
		// Attempt to delete the record
		$result = parent::destroy();
		if ($result)
		{
			Event::trigger('user.onUserAfterDelete', array($data, true, $this->getError()));
		}
		return $result;
	}
	/**
	 * Clears all terms of use agreements
	 *
	 * @return  bool
	 */
	public static function clearTerms()
	{
		$tbl = self::blank();
		$query = $tbl->getQuery()
			->update($tbl->getTableName())
			->set(array('usageAgreement' => 0));
		return $query->execute();
	}
	
	/**
	* Get user ID
	*
	* @param   string   $name
	* @return  int user ID
	*/
	public static function getUserID($name)
	{
		$db = App::get('db');
		$sql = 'SELECT id FROM #__users' . ' WHERE name = ' . '"' . $name . '"';
		$db->setQuery($sql);
		$id = (int)$db->loadResult();
		return $id;
	}
	
	/**
	* check if user has registered with system or not
	*
	* @param   string   $name
	* @return  boolean
	*/
	public static function userExists($name)
	{
		$userID = self::getUserID($name);
		if ((int)$userID != 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* Save ORCID ID to Session
	*
	* @param   string   $name
	* @param   string   $orcid
	* @return  void
	*/
	public static function saveOrcidToSession($name, $orcid)
	{
		$sesID = "session_" . str_replace(' ', '', $name);
		Session::set($sesID, $orcid);
	}
	
	/**
	* Get orcid from Session
	*
	* @param   string   $name
	* @return  orcid ID
	*/
	public static function getORCIDFromSession($name)
	{
		$sesID = "session_" . str_replace(' ', '', $name);
		$orcid = Session::get($sesID);
		return $orcid;
	}
	
	/**
	* Save orcid to #_user_profiles
	*
	* @param   string   $name, $orcid
	* @return  void
	*/
	public static function saveORCIDToProfile($name, $orcid)
	{	
		$userID = self::getUserID($name);
		$db = App::get('db');
		
		//Check if the user already has orcid record
		$exist_q = "SELECT EXISTS(SELECT * FROM #__user_profiles WHERE user_id = $userID AND profile_key = " . '"orcid"' . ")";
		$db->setQuery($exist_q);
		$exist = (int)$db->loadResult();
		
		//Get new ID based on existing maximum ID
		$id_q = "SELECT MAX(id) FROM #__user_profiles";
		$db->setQuery($id_q);
		$newID = (int)$db->loadResult() + 1;
		
		//Get new ordering based on existing maximum ordering
		$order_q = "SELECT MAX(ordering) FROM #__user_profiles where user_id = $userID";
		$db->setQuery($order_q);
		$newOrdering = (int)$db->loadResult() + 1;
		
		if ($exist == 1)
		{
			$update_q = "UPDATE #__user_profiles SET profile_value = " . '"' . $orcid  . '"' . " WHERE user_id = $userID AND profile_key = " . '"orcid"';
			$db->setQuery($update_q);
			$db->execute();
		}
		else
		{
			$orcid_sql = "INSERT INTO " . $db->quoteName('#__user_profiles') . " (" . $db->quoteName('id') . "," . $db->quoteName('user_id') . "," 
			. $db->quoteName('profile_key') . "," . $db->quoteName('profile_value') . "," . $db->quoteName('ordering') . "," . $db->quoteName('access') 
			. ") VALUES (" . $newID . "," . $userID . "," . '"orcid"'. "," . '"' . $orcid . '"' . "," . $newOrdering . "," . "1)";
			
			$db->setQuery($orcid_sql);
			$result = $db->query();
		}
	}
}