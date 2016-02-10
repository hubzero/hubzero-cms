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

use Components\Answers\Models\Tags;
use Lang;
use Date;
use User;

/**
 * Table class for a question
 */
class Question extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_questions', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->subject = trim($this->subject);
		if ($this->subject == '')
		{
			$this->setError(Lang::txt('Your question must contain a subject.'));
			return false;
		}

		// Updating entry
		$this->created    = $this->created    ?: Date::toSql();
		$this->created_by = $this->created_by ?: User::get('id');

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function buildQuery($filters=array())
	{
		if (!isset($filters['tag']))
		{
			$filters['tag'] = '';
		}

		// build body of query
		$query  = "";
		if ($filters['tag'])
		{
			$query .= "FROM $this->_tbl AS C";
			if (isset($filters['count']))
			{
				$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=C.id AND RTA.tbl='answers' ";
			}
			else
			{
				$query .= " INNER JOIN #__tags_object AS RTA ";
			}
			$query .= "INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
		}
		else
		{
			$query .= "FROM $this->_tbl AS C ";
		}
		$query .= " LEFT JOIN #__users AS U ON U.id=C.created_by ";

		switch ($filters['filterby'])
		{
			case 'mine':   $query .= "WHERE C.state!=2 "; $filters['mine'] = 1;       break;
			case 'all':    $query .= "WHERE C.state!=2 ";      break;
			case 'closed': $query .= "WHERE C.state=1 ";  break;
			case 'open':   $query .= "WHERE C.state=0 ";  break;
			case 'none':   $query .= "WHERE 1=2 ";        break;
			default:       $query .= "WHERE C.state!=2 "; break;
		}
		//$query .= "AND U.id=C.created_by ";
		if (isset($filters['q']) && $filters['q'] != '')
		{
			$words   = explode(' ', $filters['q']);
			foreach ($words as $word)
			{
				$word = $this->_db->quote('%' . strtolower($word) . '%');
				$query .= "AND ((LOWER(C.subject) LIKE $word)
					OR (LOWER(C.question) LIKE $word)
					OR (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.question_id=C.id AND (LOWER(a.answer) LIKE $word)) > 0)";
			}
		}
		if (isset($filters['mine']) && $filters['mine'] != 0)
		{
			$query .= " AND C.created_by=" . $this->_db->quote(User::get('id')) . " ";
		}
		if (isset($filters['mine']) && $filters['mine'] == 0)
		{
			$query .= " AND C.created_by!=" . $this->_db->quote(User::get('id')) . " ";
		}
		if (isset($filters['created_before']) && $filters['created_before'] != '')
		{
			$query .= " AND C.created <= " . $this->_db->quote($filters['created_before']) . " ";
		}
		if ($filters['tag'])
		{
			include_once(dirname(__DIR__) . DS . 'models' . DS . 'tags.php');
			$cloud = new Tags();
			$tags = $cloud->parse($filters['tag']);

			$query .= "AND (
							RTA.objectid=C.id
							AND RTA.tbl='answers'
							AND (
								TA.tag IN ('" . implode("','", $tags) . "') OR TA.raw_tag IN ('" . implode("','", $tags) . "')
							)
						)";

			if (!isset($filters['count']))
			{
				$query .= " GROUP BY C.id ";
			}
		}
		if (!isset($filters['count']) || !$filters['count'])
		{
			$sortdir = (isset($filters['sort_Dir'])) ? $filters['sort_Dir'] : 'DESC';
			$sortdir = $sortdir == 'DESC' ? 'DESC' : 'ASC';
			switch ($filters['sortby'])
			{
				case 'rewards':      $query .= " ORDER BY points $sortdir, C.created $sortdir"; break;
				case 'votes':        $query .= " ORDER BY C.helpful $sortdir, C.created $sortdir"; break;
				case 'date':         $query .= " ORDER BY C.created $sortdir"; break;
				case 'random':       $query .= " ORDER BY RAND()"; break;
				case 'responses':    $query .= " ORDER BY rcount DESC, C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
				case 'status':       $query .= " ORDER BY C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
				case 'withinplugin': $query .= " ORDER BY C.reward DESC, points DESC, C.state ASC, C.created DESC"; break;
				default:
					if (isset($filters['sort']))
					{
						$filters['sort_Dir'] = (isset($filters['sort_Dir'])) ? $filters['sort_Dir'] : 'DESC';
						$query .= " ORDER BY " . $filters['sort'] . " " .  $filters['sort_Dir'];
					}
					else
					{
						$query .= " ";
					}
				break;
			}
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(C.id) ";

		$filters['sortby'] = '';
		$filters['count'] = 1;
		$query .= $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getResults($filters=array())
	{
		$ar = new Response($this->_db);

		$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.state, C.anonymous, C.reward, C.helpful, U.name, U.id AS userid";
		$query .= ", (SELECT COUNT(*) FROM $ar->_tbl AS a WHERE a.state!=2 AND a.question_id=C.id) AS rcount";
		$query .= ", (SELECT SUM(tr.amount) FROM #__users_transactions AS tr WHERE tr.category='answers' AND tr.type='hold' AND tr.referenceid=C.id) AS points";
		$query .= (isset($filters['tag']) && $filters['tag']) ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$query .= $this->buildQuery($filters);
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get questions by tag
	 *
	 * @param   string   $tag    Tag to find records by
	 * @param   integer  $limit  Max number of records to return
	 * @return  array
	 */
	public function getQuestionsByTag($tag, $limit=100)
	{
		$query  = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM `#__answers_responses` AS r WHERE r.question_id=a.id) AS rcount";
		$query .= " FROM $this->_tbl AS a, #__tags_object AS RTA ";
		$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
		$query .= " WHERE RTA.objectid=a.id AND RTA.tbl='answers' AND (TA.tag=" . $this->_db->quote(strtolower($tag)) . " OR TA.raw_tag=" . $this->_db->quote($tag) . ")";

		$query .= " ORDER BY a.created DESC";
		$query .= ($limit) ? " LIMIT " . $limit : "";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the ID of question either before or after the current ID
	 *
	 * @param   integer  $id     Question ID
	 * @param   string   $which  Direction to look (prev or next)
	 * @return  integer
	 */
	public function getQuestionID($id, $which)
	{
		$query  = "SELECT a.id FROM `$this->_tbl` AS a WHERE a.state != 2 AND ";
		$query .= ($which == 'prev') ? "a.id < " . $this->_db->quote($id) . " " : "a.id > " . $this->_db->quote($id);
		$query .= ($which == 'prev') ? " ORDER BY a.id DESC "  : " ORDER BY a.id ASC ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

