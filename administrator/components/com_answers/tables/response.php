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
 * Table class for answers response
 */
class AnswersTableResponse extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $question_id        = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $answer     = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $created_by = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $helpful    = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $nothelpful = NULL;

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
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_responses', 'id', $db);
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
			$this->setError(JText::_('Missing question ID.'));
			return false;
		}

		$this->answer = trim($this->answer);
		if ($this->answer == '')
		{
			$this->setError(JText::_('Your response must contain text.'));
			return false;
		}
		$this->answer = nl2br($this->answer);

		$this->helpful    = intval($this->helpful);
		$this->nothelpful = intval($this->nothelpful);
		$this->state      = intval($this->state);

		$this->anonymous  = intval($this->anonymous);
		if ($this->anonymous > 1)
		{
			$this->anonymous = 1;
		}

		$this->created    = $this->created    ?: JFactory::getDate()->toSql();
		$this->created_by = $this->created_by ?: JFactory::getUser()->get('id');

		return true;
	}

	/**
	 * Get records based on filters
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$juser = JFactory::getUser();

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
		$ab = new ReportAbuse($this->_db);

		if (isset($filters['question_id']))
		{
			$qid = $filters['question_id'];
		}
		else
		{
			$qid = $this->question_id;
		}
		if ($qid == null)
		{
			return false;
		}

		if (!$juser->get('guest'))
		{
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= ", l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN #__answers_log AS l ON r.id=l.response_id AND ip=" . $this->_db->Quote($filters['ip']) . " WHERE r.state!=2 AND r.question_id=" . $this->_db->Quote($qid);
		}
		else
		{
			$query  = "SELECT r.*";
			$query .= ", (SELECT COUNT(*) FROM $ab->_tbl AS a WHERE a.category='answers' AND a.state=0 AND a.referenceid=r.id) AS reports";
			$query .= " FROM $this->_tbl AS r WHERE r.state!=2 AND r.question_id=" . $this->_db->Quote($qid);
		}
		$query .= " ORDER BY r.state DESC, r.created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all users and their votes for responses on a question
	 *
	 * @param   integer  $qid  Question ID
	 * @return  mixed    False on error, array on success
	 */
	public function getActions($qid=null)
	{
		$qid = $qid ?: $this->question_id;

		if ($qid == null)
		{
			return false;
		}

		$query = "SELECT id, helpful, nothelpful, state, created_by FROM `$this->_tbl` WHERE question_id=" . $this->_db->Quote($qid) . " AND state!='2'";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load a response with vote information
	 *
	 * @param      integer $id Record ID
	 * @param      string  $ip IP address
	 * @return     mixed False on error, array on success
	 */
	public function getResponse($id=null, $ip = null)
	{
		$id = $id ?: $this->id;
		$ip = $ip ?: $this->ip;

		if ($id == null || $ip == null)
		{
			return false;
		}

		$query  = "SELECT r.*, l.helpful AS vote FROM $this->_tbl AS r LEFT JOIN `#__answers_log` AS l ON r.id=l.response_id AND ip=" . $this->_db->Quote($ip) . " WHERE r.state!=2 AND r.id=" . $this->_db->Quote($id);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Set a response to "deleted"
	 *
	 * @param   integer  $id  Record ID
	 * @return  boolean  True on success
	 */
	public function deleteResponse($id=null)
	{
		$id = $id ?: $this->id;

		if ($id == null)
		{
			return false;
		}

		$query  = "UPDATE `$this->_tbl` SET state=" . $this->_db->Quote(2) . " WHERE id=" . $this->_db->Quote($id);

		$this->_db->setQuery($query);
		$this->_db->query();
		return true;
	}

	/**
	 * Get the response IDs for a question
	 *
	 * @param   integer  $qid  Question ID
	 * @return  mixed    False if error, array on success
	 */
	public function getIds($qid=null)
	{
		$qid = $qid ?: $this->question_id;

		if ($qid == null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT id FROM `$this->_tbl` WHERE question_id=" . $this->_db->Quote($qid));
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query = "FROM `$this->_tbl` AS m LEFT JOIN #__users AS u ON m.created_by=u.id";

		$where = array();

		if (isset($filters['filterby']))
		{
			switch ($filters['filterby'])
			{
				case 'all':
					$where[] = "(m.state=1 OR m.state=0)";
				break;
				case 'accepted':
					$where[] = "m.state=1";
				break;
				case 'rejected':
				default:
					$where[] = "m.state=0";
				break;
			}
		}
		else
		{
			if (isset($filters['state']))
			{
				if (is_array($filters['state']))
				{
					$filters['state'] = array_map('intval', $filters['state']);
					$where[] = "m.state IN (" . implode(',', $filters['state']) . ")";
				}
				else if ($filters['state'] >= 0)
				{
					$where[] = "m.state=" . $this->_db->Quote($filters['state']);
				}
			}
		}

		if (isset($filters['question_id']) && $filters['question_id'] > 0)
		{
			$where[] = "m.question_id=" . $this->_db->Quote($filters['question_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of, single entry, or list of entries
	 * 
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   array    $select   List of fields to select
	 * @return  mixed
	 * @since   1.3.1
	 */
	public function find($what='', $filters=array(), $select=array())
	{
		$what = strtolower($what);
		$select = (array) $select;

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;

				return $this->find('one', $filters);
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				if (empty($select))
				{
					$select = array(
						'm.*',
						'u.name'
					);
				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->_buildQuery($filters);

				if (isset($filters['sortby']) && $filters['sortby'] != '')
				{
					$query .= " ORDER BY " . $filters['sortby'];
				}
				else
				{
					if (!isset($filters['sort']))
					{
						$filters['sort'] = 'created';
					}
					if (!isset($filters['sort_Dir']))
					{
						$filters['sort_Dir'] = 'ASC';
					}
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'ASC';
					}
					if (isset($filters['sort']))
					{
						$query .= " ORDER BY " . $filters['sort'] . " " .  $filters['sort_Dir'];
					}
				}

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}
}

