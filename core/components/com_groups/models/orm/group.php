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

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Content\Moderator;
use Lang;
use Date;

require_once __DIR__ . DS . 'invitee.php';
require_once __DIR__ . DS . 'applicant.php';
require_once __DIR__ . DS . 'member.php';
require_once __DIR__ . DS . 'manager.php';
require_once __DIR__ . DS . 'page.php';
require_once __DIR__ . DS . 'module.php';
require_once __DIR__ . DS . 'role.php';
require_once __DIR__ . DS . 'log.php';

/**
 * Group model
 */
class Group extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 **/
	protected $table = '#__xgroups';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'gidNumber';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'description';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'description' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'cn'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

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
		if ($key == 'id')
		{
			$key = 'gidNumber';
		}

		// Legacy code expects get('id') to always return an integer
		if ($key == 'gidNumber' && is_null($default))
		{
			$default = 0;
		}

		return parent::get($key, $default);
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticCn($data)
	{
		$alias = (isset($data['cn']) && $data['cn'] ? $data['cn'] : $data['title']);
		$alias = str_replace(' ', '-', $alias);
		return preg_replace("/[^a-zA-Z0-9\-\_]/", '', strtolower($alias));
	}

	/**
	 * Defines a belongs to one relationship between article and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Get a list of applicants
	 *
	 * @return  object
	 */
	public function applicants()
	{
		return $this->oneToMany('Applicant', 'gidNumber');
	}

	/**
	 * Get a list of invitees
	 *
	 * @return  object
	 */
	public function invitees()
	{
		return $this->oneToMany('Invitee', 'gidNumber');
	}

	/**
	 * Get a list of members
	 *
	 * @return  object
	 */
	public function members()
	{
		return $this->oneToMany('Member', 'gidNumber');
	}

	/**
	 * Get a list of managers
	 *
	 * @return  object
	 */
	public function managers()
	{
		return $this->oneToMany('Manager', 'gidNumber');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany('Page', 'gidNumber');
	}

	/**
	 * Get a list of modules
	 *
	 * @return  object
	 */
	public function modules()
	{
		return $this->oneToMany('Module', 'gidNumber');
	}

	/**
	 * Get a list of roles
	 *
	 * @return  object
	 */
	public function roles()
	{
		return $this->oneToMany('Role', 'gidNumber');
	}

	/**
	 * Get a list of logs
	 *
	 * @return  object
	 */
	public function logs()
	{
		return $this->oneToMany('Log', 'gidNumber');
	}

	/**
	 * Is the record with the given alias unique?
	 *
	 * @return  bool
	 */
	public function isUnique()
	{
		$entries = self::all()
			->whereEquals('cn', $this->get('cn'));

		if (!$this->isNew())
		{
			$entries->where('gidNumber', '!=', $this->get('id'));
		}

		$row = $entries->row();

		return ($row->get('gidNumber') <= 0);
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove applicants
		foreach ($this->applicants()->rows() as $applicant)
		{
			if (!$applicant->destroy())
			{
				$this->addError($applicant->getError());
				return false;
			}
		}

		// Remove invitees
		foreach ($this->invitees()->rows() as $invitee)
		{
			if (!$invitee->destroy())
			{
				$this->addError($invitee->getError());
				return false;
			}
		}

		// Remove members
		foreach ($this->members()->rows() as $member)
		{
			if (!$member->destroy())
			{
				$this->addError($member->getError());
				return false;
			}
		}

		// Remove managers
		foreach ($this->managers()->rows() as $manager)
		{
			if (!$manager->destroy())
			{
				$this->addError($manager->getError());
				return false;
			}
		}

		// Remove pages
		foreach ($this->pages()->rows() as $page)
		{
			if (!$page->destroy())
			{
				$this->addError($page->getError());
				return false;
			}
		}

		// Remove modules
		foreach ($this->modules()->rows() as $module)
		{
			if (!$module->destroy())
			{
				$this->addError($module->getError());
				return false;
			}
		}

		// Remove roles
		foreach ($this->roles()->rows() as $role)
		{
			if (!$role->destroy())
			{
				$this->addError($role->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Load a single record by CN
	 *
	 * @param   string  $cn
	 * @return  object
	 */
	public static function oneByCn($cn)
	{
		return self::all()
			->whereEquals('cn', (string)$cn)
			->row();
	}

	/**
	 * Get a group's picture
	 *
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @return  string
	 */
	public function picture($thumbnail=true)
	{
		static $fallback;

		if (!isset($fallback))
		{
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64' style='stroke-width: 0px; background-color: #ffffff;'>" .
					"<path fill='#d9d9d9' d='M0,64c0,0,2.5,0,3.1,0H44H48c0,0-0.6-1.9-0.9-2.8c-0.2-0.5-0.8-1.7-1.6-3.7c-0.8-2-1.7-3.4-2.6-4.2" .
					"c-0.9-0.8-1.8-1.2-2.8-1.5l-1.7-0.2c-0.7-0.1-1.2-0.2-1.5-0.2l-6.8-1.1c-0.1-0.3-0.2-0.9-0.3-1.6c-0.1-0.7-0.2-1.5-0.4-2l0.7-0.9" .
					"c1-1.3,1.6-2.7,2-3.9c0.4-1.2,0.7-3,0.9-5.2c0.1-1.3,0.2-2.1,0.3-2.5c0.3-2.8-0.6-5.2-2.6-7.2c-2-2-4.5-3-7.3-3c-2.8,0-5.2,1-7.3,3" .
					"c-2,2-2.9,4.5-2.6,7.2c0,0.3,0.1,1.1,0.3,2.5c0.1,1.3,0.2,2.4,0.4,3.1c0.1,0.8,0.3,1.7,0.6,2.6c0.2,0.9,0.9,2,2,3.4l0.6,0.9" .
					"c-0.1,0.4-0.3,1.1-0.4,2c-0.1,0.9-0.2,1.6-0.2,1.7l0,0c-0.1,0.1-0.2,0.1-0.3,0.1l-0.8,0.1l-1.1,0.2l-2.4,0.3" .
					"c-2.7,0.4-4.2,0.7-4.5,0.7c-1.6,0.2-2.8,0.7-3.7,1.5c-0.8,0.8-1.5,1.7-1.9,2.6s-0.8,1.9-1.2,2.8c-0.3,1-0.7,1.7-0.9,2.1L0,64z" .
					" M64,62.5c0-0.1-0.1-0.4-0.4-1.1c-0.3-0.9-0.8-2.1-1.5-3.5c-0.2-0.4-0.6-1-1-1.5c-0.4-0.6-1-0.9-1.7-1.1c-0.7-0.2-1.6-0.4-2.8-0.4" .
					"l-3.5-0.4c-1.1-0.1-1.7-0.3-1.7-0.7l0,0l-0.4-2c-0.1-0.2,0-0.6,0.2-0.8c0.1,0,0.2-0.1,0.3-0.3l0.7-1c0.3-0.6,0.7-1.1,0.7-1.9" .
					"c0.1-0.7,0.2-1.6,0.4-2.7c0.1-1.1,0.2-1.9,0.3-2.5c0.2-1.3,0-2.6-0.6-3.8c-0.6-1.2-1.5-2.1-2.5-2.8c-2.7-1.6-5.2-1.7-7.4-0.4" .
					"c-1.2,0.6-2.1,1.5-2.9,2.7s-1.1,2.4-1,3.7c0,0.2,0.1,1.5,0.4,3.9l0.2,1.5c0.1,0.7,0.2,1.1,0.3,1.2c0.2,0.2,0.7,0.3,1.5,0.4" .
					"c2.6,0.4,4.5,1.9,5.8,4.2c0.6,0.9,1.2,2.2,2,4.1c0.8,1.9,1.8,4.3,1.8,4.4l0.9,2.3H64V62.5z'/>" .
					"</svg>";

			$fallback = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		if (!$this->get('gidNumber'))
		{
			return $fallback;
		}

		$picture = null;

		/* Placeholder for resolvers.
		   Ideally, this should work the same as members with
		   resolvers for things such as Identicon, Initialcon, etc.
		foreach (self::$pictureResolvers as $resolver)
		{
			$picture = $resolver->picture(
				$this->get('gidNumber'),
				$this->get('cn'),
				$thumbnail
			);

			if ($picture)
			{
				break;
			}
		}*/

		if ($logo = $this->get('logo'))
		{
			$path = PATH_APP . '/site/groups/' . $this->get('gidNumber') . '/uploads/' . $logo;

			if (is_file($path))
			{
				$picture = with(new Moderator($path))->getUrl();
			}
		}

		$picture = $picture ?: $fallback;

		return $picture;
	}
}
