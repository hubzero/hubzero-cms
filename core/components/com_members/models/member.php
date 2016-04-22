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
use Hubzero\User\Profile\Helper as ProfileHelper;

require_once(__DIR__ . DS . 'profile.php');
require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'note.php');
require_once(__DIR__ . DS . 'quota.php');
//require_once(__DIR__ . DS . 'accessgroup.php');
//require_once(__DIR__ . DS . 'accessgroup' . DS . 'map.php');

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
	 * Get access groups
	 *
	 * @return  object
	 */
	public function accessgroups()
	{
		return $this->oneToMany('Hubzero\Access\Map', 'user_id');
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
		if (!$this->hasAttribute($key) && !$this->profileLoaded)
		{
			foreach ($this->profiles()->ordered()->rows() as $field)
			{
				$this->set($field->get('profile_key'), $field->get('profile_value'));
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
	 * Get a user's picture
	 *
	 * @param   integer  $anonymous  Is user anonymous?
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @param   boolean  $serveFile  Serve file?
	 * @return  string
	 */
	public function picture($anonymous=0, $thumbnail=true, $serveFile=true)
	{
		return ProfileHelper::getMemberPhoto($this, $anonymous, $thumbnail, $serveFile);
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
		// Trigger the onUserBeforeSave event.
		//$result = Event::trigger('user.onUserBeforeSave', array($old, false, $table->getProperties()));

		// Map set data to profile fields
		$attribs = $this->getAttributes();
		$columns = $this->getStructure()->getTableColumns($this->getTableName());
		$profile = array();

		foreach ($attribs as $key => $val)
		{
			if (!isset($columns[$key]))
			{
				$field = Profile::oneByKeyAndUser($key, $this->get('id'));
				$field->set('profile_value', $val);

				$profile[$key] = $field;

				$this->removeAttribute($key);
			}
		}

		// Save record
		$result = parent::store();

		if ($result)
		{
			foreach ($profile as $field)
			{
				$result = $field->save();

				if (!$result)
				{
					$this->addError($field->getError());
					break;
				}
			}
		}

		if (!$result)
		{
			// Reset the data to the way it was before save attempt
			$this->set($attribs);
		}
		else
		{
			// Trigger the onAftereStoreUser event
			//Event::trigger('user.onUserAfterSave', array($attribs, false, true, null));
		}

		return $result;
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
				$this->setError($field->getError());
				return false;
			}
		}

		// Remove notes
		foreach ($this->notes()->rows() as $note)
		{
			if (!$note->destroy())
			{
				$this->setError($note->getError());
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
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array('usageAgreement' => 0));

		return $query->execute();
	}
}
