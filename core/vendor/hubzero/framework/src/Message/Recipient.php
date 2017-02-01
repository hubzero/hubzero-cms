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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;

/**
 * Model class for recipient of message
 */
class Recipient extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xmessage';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__xmessage_recipient';

	/**
	 * Default order by for model
	 *
	 * @var  string
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
		'mid' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between entry and message
	 *
	 * @return  object
	 */
	public function message()
	{
		return $this->belongsToOne('Message', 'mid');
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uid');
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function action()
	{
		return $this->belongsToOne('Action', 'actionid');
	}

	/**
	 * Load a record by message ID and user ID
	 *
	 * @param   integer  $mid  Message ID
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public static function oneByMessageAndUser($mid, $uid)
	{
		return self::all()
			->whereEquals('mid', $mid)
			->whereEquals('uid', $uid)
			->row();
	}

	/**
	 * Builds a query string based on filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function buildQuery($uid, $filters=array())
	{
		$r = $this->getTableName();
		$m = Message::blank()->getTableName();
		$s = Seen::blank()->getTableName();

		$entries = self::all()
			->join($m, $m . '.id', $r . '.mid', 'inner')
			//->join($s, $s . '.mid', $m . '.id', 'left')
			->joinRaw($s, $s . '.mid=' . $m . '.id AND ' . $s . '.uid=' . $uid, 'left')
			->whereEquals($r . '.uid', $uid);

		if (isset($filters['state']))
		{
			$entries->whereEquals($r . '.state', $filters['state']);
		}
		if (isset($filters['filter']) && $filters['filter'] != '')
		{
			$entries->whereEquals($m . '.component', $filters['filter']);
		}

		return $entries;
	}

	/**
	 * Get records for a user based on filters passed
	 *
	 * @param   integer  $uid      User ID
	 * @param   array    $filters  Filters to build query from
	 * @return  mixed    False if errors, array on success
	 */
	public function getMessages($uid=null, $filters=array())
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return array();
		}

		$r = $this->getTableName();
		$m = Message::blank()->getTableName();
		$s = Seen::blank()->getTableName();

		$entries = $this->buildQuery($uid, $filters);

		return $entries
			->select($m . '.*,' . $r . '.expires,' . $r . '.actionid,' . $r . '.state,' . $s . '.whenseen')
			->order($r . '.created', 'desc')
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();
	}

	/**
	 * Get a record count for a user based on filters passed
	 *
	 * @param   integer  $uid      User ID
	 * @param   array    $filters  Filters to build query from
	 * @return  mixed    False if errors, integer on success
	 */
	public function getMessagesCount($uid, $filters=array())
	{
		if (!$uid)
		{
			return 0;
		}

		$entries = $this->buildQuery($uid, $filters);

		return $entries->total();
	}

	/**
	 * Get a list of unread messages for a user
	 *
	 * @param   integer  $uid    User ID
	 * @param   integer  $limit  Number of records to return
	 * @return  mixed    False if errors, array on success
	 */
	public function getUnreadMessages($uid, $limit=null)
	{
		if (!$uid)
		{
			return array();
		}

		$r = $this->getTableName();
		$m = Message::blank()->getTableName();
		$s = Seen::blank()->getTableName();

		$entries = self::all()
			->select($m . '.*,' . $r . '.expires,' . $r . '.actionid')
			->join($m, $m . '.id', $r . '.mid', 'inner')
			->whereEquals($r . '.uid', $uid)
			->where($r . '.state', '!=', 2)
			->whereRaw($m . ".id NOT IN (SELECT s.mid FROM `" . $s . "` AS s WHERE s.uid=" . $uid . ")")
			->order($r . '.created', 'desc');

		if ($limit)
		{
			$entries->limit($limit);
		}

		return $entries->rows();
	}

	 public function getUnreadMessagesCount($uid, $limit=null)
        {
                if (!$uid)
                {
                        return array();
                }

                $r = $this->getTableName();
                $m = Message::blank()->getTableName();
                $s = Seen::blank()->getTableName();

                $entries = self::all()
                        ->select($m . '.*,' . $r . '.expires,' . $r . '.actionid')
                        ->join($m, $m . '.id', $r . '.mid', 'inner')
                        ->whereEquals($r . '.uid', $uid)
                        ->where($r . '.state', '!=', 2)
                        ->whereRaw($m . ".id NOT IN (SELECT s.mid FROM `" . $s . "` AS s WHERE s.uid=" . $uid . ")")
                        ->order($r . '.created', 'desc');

                if ($limit)
                {
                        $entries->limit($limit);
                }

                return $entries->count();
        }


	/**
	 * Delete all messages marked as trash for a user
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function deleteTrash($uid)
	{
		return $this->delete()
			->whereEquals('uid', $uid)
			->whereEquals('state', 2)
			->execute();
	}

	/**
	 * Set the state of multiple messages
	 *
	 * @param   integer  $state  State to set
	 * @param   array    $ids    List of message IDs
	 * @return  boolean  True on success
	 */
	public function setState($state=0, $ids=array())
	{
		if (count($ids) <= 0)
		{
			return false;
		}

		$ids = array_map('intval', $ids);

		return $this->update()
			->set(array('state' => $state))
			->whereIn('id', $ids)
			->execute();
	}

	/**
	 * Mark a message as being read by the recipient
	 *
	 * @return  boolean  True on success
	 */
	public function markAsRead()
	{
		if (!$this->get('id'))
		{
			$this->addError('Recipient record not found');
			return false;
		}

		$xseen = Seen::oneByMessageAndUser($this->get('mid'), $this->get('uid'));

		if ($xseen->get('whenseen') == ''
		 || $xseen->get('whenseen') == '0000-00-00 00:00:00'
		 || $xseen->get('whenseen') == null)
		{
			$dt = new Date('now');

			$xseen->set('mid', $this->get('mid'));
			$xseen->set('uid', $this->get('uid'));
			$xseen->set('whenseen', $dt->toSql());
			if (!$xseen->save())
			{
				$this->addError($xseen->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Mark a message as not being read by the recipient
	 *
	 * @return  boolean  True on success
	 */
	public function markAsUnread()
	{
		if (!$this->get('id'))
		{
			$this->addError('Recipient record not found');
			return false;
		}

		$xseen = Seen::oneByMessageAndUser($this->get('mid'), $this->get('uid'));

		if ($xseen->get('id'))
		{
			if (!$xseen->destroy())
			{
				$this->addError($xseen->getError());
				return false;
			}
		}

		return true;
	}
}
