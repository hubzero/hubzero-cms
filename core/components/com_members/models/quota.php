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

use Hubzero\Database\Relational;
use Components\Members\Models\Quota\Category;
use Components\Members\Models\Quota\Log;
use User;
use Lang;

include_once __DIR__ . DS . 'quota' . DS . 'log.php';

/**
 * User quota model
 */
class Quota extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'users';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'user_id';

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
		'user_id'  => 'positive|nonzero',
		'class_id' => 'positive|nonzero'
	);

	/**
	 * Get parent user
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Member', 'user_id');
	}

	/**
	 * Get parent quota category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->oneToOne('Components\Members\Models\Quota\Category', 'class_id');
	}

	/**
	 * Override save to add logging
	 *
	 * @return  boolean
	 */
	public function save()
	{
		// Use getInstance, rather than User::get('username'), as existing
		// user object won't get the right username if it was just updated
		$username = $this->member()->get('username');

		// Don't try to save quotas for auth link temp accounts (negative number usernames)
		if (is_numeric($username) && $username < 0)
		{
			return false;
		}

		$action = ($this->get('id') ? 'modify' : 'add');

		$result = parent::save();

		if ($result)
		{
			$command = "update_quota '" . $this->get('user_id') . "' '" . $this->get('soft_blocks') . "' '" . $this->get('hard_blocks') . "'";

			$cmd = "/bin/sh " . PATH_CORE . "/components/com_tools/scripts/mw {$command} 2>&1 </dev/null";

			exec($cmd, $results, $status);

			// Check exec status
			if (!isset($status) || $status != 0)
			{
				// Something went wrong
				$this->addError(Lang::txt('COM_MEMBERS_QUOTA_USER_FAILED_TO_SAVE_TO_FILESYSTEM'));
				return false;
			}

			$log = Log::blank();
			$log->set('object_type', 'class');
			$log->set('object_id', $this->get('id'));
			$log->set('name', $this->get('alias'));
			$log->set('action', $action);
			$log->set('actor_id', User::get('id'));
			$log->set('soft_blocks', $this->get('soft_blocks'));
			$log->set('hard_blocks', $this->get('hard_blocks'));
			$log->set('soft_files', $this->get('soft_files'));
			$log->set('hard_files', $this->get('hard_files'));
			$log->save();
		}

		return $result;
	}

	/**
	 * Upon deletion of a class, restore all users of that class to the default class
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public function restoreDefaultClass($id)
	{
		$deflt = Category::defaultEntry();

		if (!$deflt->get('id'))
		{
			return false;
		}

		$records = self::all()
			->whereEquals('class_id', $id)
			->rows();

		if ($records->count() > 0)
		{
			// Build an array of ids
			$ids = array();

			foreach ($records as $record)
			{
				// Update their class id, and their actual quota will be
				// updated the next time they log in.
				$record->set('class_id', (int)$deflt->get('id'));
				$record->save();
			}
		}

		return true;
	}

	/**
	 * Set default class for given set of users
	 *
	 * @param   array  $users
	 * @return  boolean
	 */
	public function setDefaultClass($users)
	{
		$deflt = Category::defaultEntry();

		if (!$deflt->get('id'))
		{
			return false;
		}

		if ($users && count($users) > 0)
		{
			// Update their class id, and their actual quota will be
			// updated the next time they log in.
			$result = $this->getQuery()
				->update($this->getTableName())
				->set(array('class_id' => (int)$deflt->get('id')))
				->whereIn('id', $users)
				->execute();

			if (!$result)
			{
				return false;
			}
		}

		return true;
	}
}
