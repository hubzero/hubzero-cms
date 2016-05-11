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

/**
 * User host model
 */
class Host extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xprofiles';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__xprofiles_host';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'uidNumber';

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
		'host'      => 'notempty',
		'uidNumber' => 'positive|nonzero'
	);

	/**
	 * Get parent member
	 *
	 * @return  object
	 */
	public function member()
	{
		return $this->belongsToOne('Member', 'uidNumber');
	}

	/**
	 * Delete this object and its dependencies
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		$query = $this->getQuery()
			->delete($this->getTableName())
			->whereEquals('host', $this->get('host'))
			->whereEquals('uidNumber', $this->get('uidNumber'));

		if (!$query->execute())
		{
			$this->addError($query->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by host name
	 *
	 * @param   mixed    $host  String or array of strings
	 * @return  boolean
	 */
	public static function destroyByHost($host)
	{
		$host = (is_array($host) ? $host : array($host));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('host', $host);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by User ID
	 *
	 * @param   mixed    $user_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function destroyByUser($user_id)
	{
		$user_id = (is_array($user_id) ? $user_id : array($user_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('uidNumber', $user_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load a record by host and user ID
	 *
	 * @param   string   $host
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByHostAndUser($host, $user_id)
	{
		return self::all()
			->whereEquals('host', $host)
			->whereEquals('uidNumber', $user_id)
			->row();
	}

	/**
	 * Add a user to access groups
	 *
	 * @param   mixed    $user_id  Integer
	 * @param   mixed    $hosts    String or array of strings
	 * @return  boolean
	 */
	public static function addUserToHost($user_id, $hosts)
	{
		// Get the user's existing entries
		$entries = self::all()
			->whereEquals('uidNumber', $user_id)
			->rows();

		$existing = array();
		foreach ($entries as $entry)
		{
			$existing[] = $entry->get('host');
		}

		$hosts = (is_array($hosts) ? $hosts : array($hosts));

		$blank = self::blank();

		// Loop through groups to be added
		foreach ($hosts as $host)
		{
			// Is the group already an existing entry?
			if (in_array($host, $existing))
			{
				// Skip.
				continue;
			}

			$query = $blank->getQuery()
				->insert($blank->getTableName())
				->values(array(
					'uidNumber' => $user_id,
					'host'      => $host
				));

			if (!$query->execute())
			{
				return false;
			}
		}

		return true;
	}
}
