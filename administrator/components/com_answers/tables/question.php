<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for a question
 */
class AnswersTableQuestion extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $subject    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $question   = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $state      = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $anonymous  = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $email      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $helpful    = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $reward    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_questions', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->subject = trim($this->subject);
		if ($this->subject == '') 
		{
			$this->setError(JText::_('Your question must contain a subject.'));
			return false;
		}

		// Updating entry
		$juser =& JFactory::getUser();
		$this->created    = $this->created    ? $this->created    : date("Y-m-d H:i:s");
		$this->created_by = $this->created_by ? $this->created_by : $juser->get('username');

		// Code cleaner
		$this->question = nl2br($this->question);

		return true;
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$juser =& JFactory::getUser();

		// build body of query
		$query  = "";
		if ($filters['tag']) 
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'tags.php');
			$tagging = new AnswersTags($this->_db);

			$query .= "FROM $this->_tbl AS C";
			if (isset($filters['count'])) 
			{
				$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=C.id AND RTA.tbl='answers' ";
			} 
			else 
			{
				$query .= ", #__tags_object AS RTA ";
			}
			$query .= "INNER JOIN $tagging->_tag_tbl AS TA ON TA.id=RTA.tagid ";
		} 
		else 
		{
			$query .= "FROM $this->_tbl AS C ";
		}
		$query .= ", #__users AS U ";

		switch ($filters['filterby'])
		{
			case 'mine':   $query .= "WHERE C.state!=2 "; $filters['mine'] = 1;       break;
			case 'all':    $query .= "WHERE C.state!=2 ";      break;
			case 'closed': $query .= "WHERE C.state=1 ";  break;
			case 'open':   $query .= "WHERE C.state=0 ";  break;
			case 'none':   $query .= "WHERE 1=2 ";        break;
			default:       $query .= "WHERE C.state!=2 "; break;
		}
		$query .= "AND U.username=C.created_by ";
		if (isset($filters['q']) && $filters['q'] != '') 
		{
			$words   = explode(' ', $filters['q']);
			foreach ($words as $word)
			{
				$word = $this->_db->getEscaped(strtolower($word));
				$query .= "AND ((LOWER(C.subject) LIKE '%$word%') 
					OR (LOWER(C.question) LIKE '%$word%') 
					OR (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.qid=C.id AND (LOWER(a.answer) LIKE '%$word%')) > 0)";
			}
		}
		if (isset($filters['mine']) && $filters['mine'] != 0) 
		{
			$query .= " AND C.created_by=" . $this->_db->Quote($juser->get('username')) . " ";
		}
		if (isset($filters['mine']) && $filters['mine'] == 0) 
		{
			$query .= " AND C.created_by!=" . $this->_db->Quote($juser->get('username')) . " ";
		}
		if (isset($filters['created_before']) && $filters['created_before'] != '') 
		{
			$query .= " AND C.created <= " . $this->_db->Quote($filters['created_before']) . " ";
		}
		if ($filters['tag']) 
		{
			$tags = $tagging->_parse_tags($filters['tag']);

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
		switch ($filters['sortby'])
		{
			case 'rewards':      $query .= " ORDER BY C.reward DESC, points DESC, C.created DESC"; break;
			case 'votes':        $query .= " ORDER BY C.helpful DESC, C.created DESC"; break;
			case 'date':         $query .= " ORDER BY C.created DESC"; break;
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

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
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
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getResults($filters=array())
	{
		$ar = new AnswersTableResponse($this->_db);

		$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.state, C.anonymous, C.reward, C.helpful, U.name, U.id AS userid";
		$query .= ", (SELECT COUNT(*) FROM $ar->_tbl AS a WHERE a.state!=2 AND a.qid=C.id) AS rcount";
		$query .= ", (SELECT SUM(tr.amount) FROM #__users_transactions AS tr WHERE tr.category='answers' AND tr.type='hold' AND tr.referenceid=C.id) AS points";
		$query .= ($filters['tag']) ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$query .= $this->buildQuery($filters);
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get questions by tag
	 * 
	 * @param      string  $tag   Tag to find records by
	 * @param      integer $limit Max number of records to return
	 * @return     array
	 */
	public function getQuestionsByTag($tag, $limit=100)
	{
		$query = "SELECT a.id, a.subject, a.question, a.state, a.created, a.created_by, a.anonymous, (SELECT COUNT(*) FROM #__answers_responses AS r WHERE r.qid=a.id) AS rcount";
		//$query.= "\n FROM $this->_tbl AS a, #__answers_tags AS t, #__tags AS tg";
		$query .= " FROM $this->_tbl AS a, #__tags_object AS RTA ";
		$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
		$query .= " WHERE RTA.objectid=a.id AND RTA.tbl='answers' AND (TA.tag='" . $this->_db->getEscaped(strtolower($tag)) . "' OR TA.raw_tag='" . $this->_db->getEscaped($tag) . "')";

		$query .= " ORDER BY a.created DESC";
		$query .= ($limit) ? " LIMIT " . $limit : "";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the ID of question either before or after the current ID
	 * 
	 * @param      integer $id    Question ID
	 * @param      string  $which Direction to look (prev or next)
	 * @return     integer
	 */
	public function getQuestionID($id, $which)
	{
		$query  = "SELECT a.id ";
		$query .= "FROM $this->_tbl AS a ";
		$query .= "WHERE a.state != 2 AND ";
		$query .= ($which == 'prev') ? "a.id < " . $this->_db->Quote($id) . " " : "a.id > " . $this->_db->Quote($id);
		$query .= ($which == 'prev') ? " ORDER BY a.id DESC "  : " ORDER BY a.id ASC ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

