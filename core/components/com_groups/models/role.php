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

namespace Components\Groups\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\User\Group;

include_once(__DIR__ . DS . 'member' . DS . 'role.php');

/**
 * Group role
 */
class Role extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'xgroups';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'name';

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
		'name'      => 'notempty',
		'gidNumber' => 'positive|nonzero'
	);

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return Group::getInstance($this->get('gidNumber'));
	}

	/**
	 * Get group permissions
	 *
	 * @return  object
	 */
	public function transformPermissions()
	{
		$registry = new Registry($this->get('permissions'));
		$registry->separator = '/';
		return $registry;
	}

	/**
	 * Get a list of members
	 *
	 * @return  object
	 */
	public function members()
	{
		return $this->oneToMany('\Components\Groups\Models\Member\Role', 'roleid');
	}

	/**
	 * Save the record
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		if (!is_string($this->get('permissions')))
		{
			$this->set('permissions', json_encode($this->get('permissions')));
		}

		return parent::save();
	}

	/**
	 * Delete record and associated content
	 *
	 * @return  object
	 */
	public function destroy()
	{
		foreach ($this->members()->rows() as $member)
		{
			if (!$member->destroy())
			{
				$this->addError($member->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
