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
		return $this->oneToMany(__NAMESPACE__ . '\\Applicant', 'gidNumber');
	}

	/**
	 * Get a list of invitees
	 *
	 * @return  object
	 */
	public function invitees()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Invitee', 'gidNumber');
	}

	/**
	 * Get a list of members
	 *
	 * @return  object
	 */
	public function members()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Member', 'gidNumber');
	}

	/**
	 * Get a list of managers
	 *
	 * @return  object
	 */
	public function managers()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Manager', 'gidNumber');
	}

	/**
	 * Get a list of categories
	 *
	 * @return  object
	 */
	public function categories()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page\\Category', 'gidNumber');
	}

	/**
	 * Get a list of pages
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Page', 'gidNumber');
	}

	/**
	 * Get a list of modules
	 *
	 * @return  object
	 */
	public function modules()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Module', 'gidNumber');
	}

	/**
	 * Get a list of roles
	 *
	 * @return  object
	 */
	public function roles()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Role', 'gidNumber');
	}

	/**
	 * Get a list of logs
	 *
	 * @return  object
	 */
	public function logs()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Log', 'gidNumber');
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
}
