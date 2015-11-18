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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Tables;

use Hubzero\Utility\Validate;
use User;
use Date;
use Lang;

/**
 * Table class for votes
 */
class Vote extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__vote_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->referenceid = intval($this->referenceid);
		if (!$this->referenceid)
		{
			$this->setError(Lang::txt('Missing reference ID'));
		}

		$this->category = trim($this->category);
		if (!$this->category)
		{
			$this->setError(Lang::txt('Missing category'));
		}

		if (!$this->id)
		{
			$this->voted = ($this->voted) ? $this->voted : Date::toSql();
			$this->voter = ($this->voter) ? $this->voter : User::get('id');
		}

		if (!Validate::ip($this->ip))
		{
			$this->setError(Lang::txt('Invalid IP address'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if a user has voted on an item
	 *
	 * @param   integer  $refid     Reference ID
	 * @param   string   $category  Reference type
	 * @param   integer  $voter     User ID
	 * @return  mixed    False on error, integer on success
	 */
	public function checkVote($refid=null, $category=null, $voter=null)
	{
		if ($refid == null)
		{
			$refid = $this->referenceid;
		}
		if ($refid == null)
		{
			return false;
		}
		if ($category == null)
		{
			$category = $this->category;
		}
		if ($category == null)
		{
			return false;
		}

		$query = "SELECT count(*) FROM $this->_tbl WHERE referenceid=" . $this->_db->quote($refid) . " AND category = " . $this->_db->quote($category) . " AND voter=" . $this->_db->quote($voter);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Load a vote record
	 *
	 * @param   integer  $refid     Reference ID
	 * @param   string   $category  Reference type
	 * @param   integer  $voter     User ID
	 * @return  mixed    False on error, integer on success
	 */
	public function loadVote($refid=null, $category=null, $voter=null)
	{
		$fields = array(
			'referenceid' => $refid,
			'category'    => $category,
			'voter'       => $voter
		);

		return parent::load($fields);
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getResults($filters=array())
	{
		$query = "SELECT c.*
				FROM $this->_tbl AS c
				WHERE c.referenceid=" . $this->_db->quote($filters['id']) . " AND category=" . $this->_db->quote($filters['category']) . " ORDER BY c.voted DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete vote record(s)
	 *
	 * @param   integer  $refid     Reference ID
	 * @param   string   $category  Reference type
	 * @param   integer  $voter     User ID
	 * @return  mixed    False on error, integer on success
	 */
	public function deleteVotes($refid=null, $category=null)
	{
		$query = "DELETE FROM $this->_tbl WHERE referenceid=" . $this->_db->quote($refid) . " AND category = " . $this->_db->quote($category);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}

