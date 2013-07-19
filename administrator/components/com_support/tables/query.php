<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for support queries
 */
class SupportQuery extends JTable 
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
	var $title      = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $conditions = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id    = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $sort       = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $sort_dir    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $iscore    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db) 
	{
		parent::__construct('#__support_queries', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check() 
	{
		$this->title = trim($this->title);
		if (!$this->title) 
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD'));
			return false;
		}

		$this->conditions = trim($this->conditions);
		if (!$this->conditions) 
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD'));
			return false;
		}
		$this->query = $this->getQuery($this->conditions);

		$this->sort = trim($this->sort);
		if (!$this->sort) 
		{
			$this->sort = 'created';
		}

		$this->sort_dir = strtolower(trim($this->sort_dir));
		if (!$this->sort_dir) 
		{
			$this->sort_dir = 'desc';
		}

		if (!$this->id) 
		{
			//$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			//$this->created_by = $juser->get('id');
		}
		if ($this->iscore === null)
		{
			$this->iscore = 0;
		}

		return true;
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array()) 
	{
		$query  = "FROM $this->_tbl AS q"; //" LEFT JOIN #__users AS u ON u.id=q.user_id";

		$where = array();
		if (isset($filters['user_id'])) 
		{
			$where[] = "q.user_id='" . $filters['user_id'] . "'";
		}
		if (isset($filters['iscore'])) 
		{
			if (is_array($filters['iscore']))
			{
				$filters['iscore'] = array_map('intval', $filters['iscore']);
				$where[] = "q.iscore IN (" . implode(',', $filters['iscore']) . ")";
			}
			else 
			{
				$where[] = "q.iscore='" . $filters['iscore'] . "'";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (!isset($filters['count']) || !$filters['count'])
		{
			if (!isset($filters['sort']) || !$filters['sort']) 
			{
				$filters['sort'] = 'created';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
			{
				$filters['sort_Dir'] = 'desc';
			}
			$query .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
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
		$filters['count'] = true;
		$filters['limit'] = 0;

		$query = "SELECT COUNT(q.id) " . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT q.* " . $this->_buildQuery($filters); //, u.name 
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get common queries
	 * 
	 * @return     array
	 */
	public function getCommonNotInACL()
	{
		$filters = array(
			'iscore'   => 4,
			'sort'     => 'id',
			'sort_Dir' => 'asc'
		);

		$query = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get common queries
	 * 
	 * @return     array
	 */
	public function getCommon()
	{
		$filters = array(
			'iscore'   => 2,
			'sort'     => 'id',
			'sort_Dir' => 'asc'
		);

		$query = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get my queries
	 * 
	 * @return     array
	 */
	public function getMine()
	{
		$filters = array(
			'iscore'   => 1,
			'sort'     => 'id',
			'sort_Dir' => 'asc'
		);

		$query = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get my queries
	 * 
	 * @return     array
	 */
	public function getCustom($user_id=null)
	{
		if (!$user_id)
		{
			$user_id = $this->user_id;
		}
		if (!$user_id)
		{
			$juser =& JFactory::getUser();
			$user_id = $juser->get('id');
		}
		$filters = array(
			'user_id'  => $user_id,
			'iscore'   => 0,
			'sort'     => 'created',
			'sort_Dir' => 'asc'
		);

		$query = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Recursive method to parse the condition and generate the query. Takes the selector for the root condition
	 * 
	 * @param      mixed $condition Accepts either a JSON string or object
	 * @return     string
	 */
	public function getCondition($data) 
	{
		return json_encode($data);
	}

	/**
	 * Recursive method to iterate over the condition tree and generate the query
	 * 
	 * @param      mixed $condition Accepts either a JSON string or object
	 * @return     string
	 */
	public function getQuery($condition) 
	{
		if (is_string($condition))
		{
			$condition = json_decode($condition);
		}
		$juser =& JFactory::getUser();

		$op = ' ' . strtoupper($condition->operator) . ' ';

		$having = '';
		$e = array();
		$elen = count($condition->expressions);

		$tags = array();
		$nottags = array();
		for ($i = 0; $i < $elen; $i++) 
		{
			$expr = $condition->expressions[$i];
			if (strtolower($expr->fldval) == 'tag') 
			{
				$tags[] = $expr->val;

				if ($expr->opval == '!=')
				{
					$nottags[] = $expr->val;
				}
			}
		}

		for ($i = 0; $i < $elen; $i++) 
		{
			$expr = $condition->expressions[$i];
			switch ($expr->opval)
			{
				case 'lt': $expr->opval = '<'; break;
				case 'lt=': $expr->opval = '<='; break;
				case 'gt': $expr->opval = '>'; break;
				case 'gt=': $expr->opval = '>='; break;
				default: break;
			}

			//$prfx = (strtolower($expr->fldval) == 'tag') ? 't' : 'f';
			$prfx = 'f';
			if (strtolower($expr->fldval) == 'tag')
			{
				continue;
				$prfx = 't';
				if (count($tags) > 1 && strtoupper($condition->operator) == 'AND')
				{
					// Skip adding multiple tags for AND conditions
					// We need to do an IN () later
					continue;
				}
			}

			if (strtoupper($expr->val) == 'NULL' || strtoupper($expr->val) == 'NULL')
			{
				$expr->opval = ($expr->opval == '=') ? 'IS $1' : 'IS NOT $1';
			}
			else if ($expr->val == '*')
			{
				$expr->opval = 'IN ($1)';

				if ($expr->fldval == 'group') 
				{
					ximport('Hubzero_User_Helper');
					$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'members');
					$expr->val = '';
					if ($xgroups) 
					{
						$g = array();
						foreach ($xgroups as $xgroup)
						{
							$g[] = $xgroup->cn;
						}
						$expr->val = "'" . implode("','", $g) . "'";
					}
					else
					{
						continue;
					}
				}
			}

			if (strstr($expr->opval, '$1')) 
			{
				if (strtolower($expr->fldval) == 'tag')
				{
					//$e[] = '(' . $prfx . '.' . $this->_db->nameQuote($expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval) . ' OR ' . $prfx . '.' . $this->_db->nameQuote('raw_' . $expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval) . ')';
				}
				else
				{
					$e[] = $prfx . '.' . $this->_db->nameQuote($expr->fldval) . ' ' . str_replace('$1', $expr->val, $expr->opval);
				}
			} 
			else 
			{
				if ($expr->val == '$me') 
				{
					$expr->val = $juser->get('username');
				} 
				$e[] = $prfx . '.' . $this->_db->nameQuote($expr->fldval) . ' ' . $expr->opval . ' ' . $this->_db->Quote($expr->val);
			}
		}
		if (count($tags) > 0)
		{
			if (implode("','", $tags) == implode("','", $nottags))
			{
				$e[] = 'f.' . $this->_db->nameQuote('id') . ' NOT IN (
							SELECT st.' . $this->_db->nameQuote('objectid') . ' FROM #__tags_object AS st 
							LEFT JOIN #__tags AS t ON st.' . $this->_db->nameQuote('tagid') . '=t.' . $this->_db->nameQuote('id') . ' 
							WHERE st.' . $this->_db->nameQuote('tbl') . '=\'support\' 
							AND (t.' . $this->_db->nameQuote('tag') . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ' OR t.' . $this->_db->nameQuote('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . '))';
				$having = " GROUP BY f.id ";
			}
			else
			{
				$e[] = '(t.' . $this->_db->nameQuote('tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ' OR t.' . $this->_db->nameQuote('raw_tag') . ' ' . str_replace('$1', "'" . implode("','", $tags) . "'", 'IN ($1)') . ')';

				if (strtoupper($condition->operator) == 'OR')
				{
					$h = 1;
				}
				else
				{
					$h = (count($tags) - count($nottags));
				}
				$having = " GROUP BY f.id HAVING uniques='" . $h . "'";
			}
		}

		$n = array();
		$nlen = count($condition->nestedexpressions);
		for ($k = 0; $k < $nlen; $k++) 
		{
			$nestexpr = $condition->nestedexpressions[$k];
			$result = $this->getQuery($nestexpr);
			$n[] = $result;
		}

		$q = array();
		if (count($e) > 0)
		{
			$q[] = implode($op, $e);
		}
		if (count($n) > 0)
		{
			$q[] = implode($op, $n);
		}

		return '(' . implode($op, $q) . ')' . $having;
	}

	/**
	 * Recursive method to iterate over the condition tree and generate the query
	 * 
	 * @param      mixed $condition Accepts either a JSON string or object
	 * @return     string
	 */
	public function getFilters($condition) 
	{
		if (is_string($condition))
		{
			$condition = json_decode($condition);
		}

		//$op = ' ' . strtoupper($condition->operator) . ' ';

		$e = array();
		$elen = count($condition->expressions);
		for ($i = 0; $i < $elen; $i++) 
		{
			$expr = $condition->expressions[$i];
			//$prfx = (strtolower($expr->fldval) == 'tag') ? 't' : 'f';
			if (strstr($expr->val, '$1')) 
			{
				$e[] = $prfx . '.' . $this->_db->nameQuote($expr->fldval) . ' ' . $expr->opval . ' ' . str_replace('$1', $expr->val);
			} 
			else 
			{
				$e[] = $prfx . '.' . $this->_db->nameQuote($expr->fldval) . ' ' . $expr->opval . ' ' . $this->_db->Quote($expr->val);
			}
		}

		$n = array();
		$nlen = count($condition->nestedexpressions);
		for ($k = 0; $k < $nlen; $k++) 
		{
			$nestexpr = $condition->nestedexpressions[$k];
			$result = $this->getQuery($nestexpr);
			$n[] = $result;
		}

		$q = array();
		if (count($e) > 0)
		{
			$q[] = implode($op, $e);
		}
		if (count($n) > 0)
		{
			$q[] = implode($op, $n);
		}

		return '(' . implode($op, $q) . ')';
	}

	/**
	 * Populate the database with default values
	 * 
	 * @param      string  $type Type of query to populate [common, mine]
	 * @return     boolean False if errors, True on success
	 */
	public function populateDefaults($type='common') 
	{
		$me = '$me';

		switch (strtolower(trim($type)))
		{
			case 'common':
				$method = 'getCommon';
				$sql = "INSERT INTO $this->_tbl (`id`, `title`, `conditions`, `query`, `user_id`, `sort`, `sort_dir`, `created`, `iscore`)
				VALUES
					(null,'Open tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[]}',NULL,0,'created','desc','2012-07-18 00:00:00',2),
					(null,'New tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"status\",\"flddisp\":\"Status\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[]}',NULL,0,'created','desc','2012-08-09 00:00:00',2),
					(null,'Unassigned','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"\"},{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"NULL\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2012-08-09 00:00:00',2),
					(null,'Awaiting User Action','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"status\",\"flddisp\":\"Status\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"2\"}],\"nestedexpressions\":[]}',NULL,0,'created','desc','2012-08-09 00:00:00',3),
					(null,'Closed tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[]}',NULL,0,'created','desc','2012-08-09 00:00:00',2),
					(null,'All tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[]}',NULL,0,'created','desc','2012-08-09 00:00:00',3);";
			break;

			case 'commonnotacl':
				$method = 'getCommonNotInACL';
				$sql = "INSERT INTO $this->_tbl (`id`, `title`, `conditions`, `query`, `user_id`, `sort`, `sort_dir`, `created`, `iscore`)
				VALUES
					(null,'Open tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"group\",\"flddisp\":\"Group\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"*\"},{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"},{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2013-01-30 08:06:10',4),
					(null,'New tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"status\",\"flddisp\":\"Status\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"group\",\"flddisp\":\"Group\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"*\"},{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"},{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2013-01-30 08:32:25',4),
					(null,'Unassigned','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"group\",\"flddisp\":\"Group\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"*\"},{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]},{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"\"},{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"NULL\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2013-01-30 08:42:42',4),
					(null,'Closed tickets','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"group\",\"flddisp\":\"Group\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"*\"},{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"},{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2013-01-30 08:32:25',4);";
			break;

			case 'mine':
				$method = 'getMine';
				$sql = "INSERT INTO $this->_tbl (`id`, `title`, `conditions`, `query`, `user_id`, `sort`, `sort_dir`, `created`, `iscore`)
				VALUES
					(null,'Reported by me','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2012-08-09 00:00:00',1),
					(null,'Assigned to me','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"1\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2012-08-09 00:00:00',1),
					(null,'Assigned to me (closed)','{\"operator\":\"AND\",\"expressions\":[{\"fldval\":\"open\",\"flddisp\":\"Open/Closed\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"},{\"fldval\":\"type\",\"flddisp\":\"Type\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"0\"}],\"nestedexpressions\":[{\"operator\":\"OR\",\"expressions\":[{\"fldval\":\"owner\",\"flddisp\":\"Owner\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"},{\"fldval\":\"login\",\"flddisp\":\"Submitter\",\"opval\":\"=\",\"opdisp\":\"is\",\"val\":\"$me\"}],\"nestedexpressions\":[]}]}',NULL,0,'created','desc','2012-08-09 00:00:00',3);";
			break;

			default:
				$this->setError(JText::_('Unsupported type'));
				return false;
			break;
		}

		$this->_db->setQuery($sql);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $this->$method();
	}
}
