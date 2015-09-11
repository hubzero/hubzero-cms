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

namespace Components\Answers\Tables;

use Hubzero\Utility\Validate;
use Lang;
use Date;
use User;

/**
 * Table class for question votes
 */
class QuestionsLog extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_questions_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->question_id = intval($this->question_id);
		if (!$this->question_id)
		{
			$this->setError(Lang::txt('Missing question ID'));
		}

		$this->voter = intval($this->voter);
		if (!$this->voter)
		{
			$this->voter = User::get('id');
		}

		if (!$this->expires)
		{
			$this->expires = Date::of(time() + (7 * 24 * 60 * 60))->toSql(); // in a week
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
	 * Check if a user has voted
	 *
	 * @param   integer  $qid    Question ID
	 * @param   string   $ip     IP address
	 * @param   integer  $voter  Voter user ID
	 * @return  mixed    False if error, integer on success
	 */
	public function checkVote($qid=null, $ip=null, $voter=null)
	{
		if ($qid == null)
		{
			$qid = $this->question_id;
		}
		if ($qid == null)
		{
			return false;
		}

		$query = "SELECT COUNT(*) FROM `$this->_tbl` WHERE question_id=" . $this->_db->quote($qid);
		if ($voter !== null)
		{
			$query .= " AND voter=" . $this->_db->quote($voter);
		}
		else
		{
			$query .= " AND ip=" . $this->_db->quote($ip);
		}


		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

