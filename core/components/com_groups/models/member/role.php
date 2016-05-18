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

namespace Components\Groups\Models\Member;

use Hubzero\Database\Relational;

/**
 * Group member role
 */
class Role extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'xgroups_member';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'id';

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
		'roleid'    => 'positive|nonzero',
		'uidNumber' => 'positive|nonzero'
	);

	/**
	 * Get associated role
	 *
	 * @return  object
	 */
	public function role()
	{
		return $this->belongsToOne('\Components\Groups\Models\Role', 'roleid');
	}

	/**
	 * Member profile
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}

	/**
	 * Member profile
	 *
	 * @return  object
	 */
	public static function oneByUserAndRole($uidNumber, $roleid)
	{
		return self::all()
			->whereEquals('uidNumber', $uidNumber)
			->whereEquals('roleid', $roleid)
			->row();
	}

	/**
	 * Remove records by user ID
	 *
	 * @param   integer  $user_id
	 * @return  boolean  False if error, True on success
	 */
	public static function destroyByUser($user_id)
	{
		$rows = self::all()
			->whereEquals('uidNumber', $user_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove records by role ID
	 *
	 * @param   integer  $role_id
	 * @return  boolean  False if error, True on success
	 */
	public static function destroyByRole($role_id)
	{
		$rows = self::all()
			->whereEquals('roleid', $role_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}
}
