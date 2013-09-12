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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Blog Entry database class
 */
class BlogTableEntry extends JTable
{

	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id           = NULL;

	/**
	 * varchar(150)
	 * 
	 * @var string
	 */
	var $title        = NULL;

	/**
	 * varchar(150)
	 * 
	 * @var string
	 */
	var $alias        = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content      = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by   = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $state        = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_up   = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $publish_down = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $group_id     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $hits         = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $allow_comments = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $scope        = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__blog_entries', 'id', $db);
	}

	/**
	 * Load an entry from the database and bind to $this
	 * 
	 * @param      string  $oid        Entry alias
	 * @param      string  $scope      Entry scope [site, group, member]
	 * @param      integer $created_by Entry author..
	 * @param      integer $group_id   Group the entry belongs to (if any)
	 * @return     boolean True if data was retrieved and loaded
	 */
	public function loadAlias($oid=NULL, $scope=NULL, $created_by=NULL, $group_id=NULL)
	{
		if ($oid === NULL) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}
		if ($scope === NULL) 
		{
			$scope = $this->scope;
		}
		if (!$scope) 
		{
			$this->setError(JText::_('Missing argument.'));
			return false;
		}
		switch ($scope)
		{
			case 'member':
				if ($created_by === NULL) 
				{
					$created_by = $this->created_by;
				}
				if (!$created_by) 
				{
					$this->setError(JText::_('Missing argument.'));
					return false;
				}
				$query = "SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid) . " AND scope=" . $this->_db->Quote($scope) . " AND created_by=" . $this->_db->Quote($created_by);
			break;

			case 'group':
				if ($group_id === NULL) {
					$group_id = $this->group_id;
				}
				if (!$group_id) 
				{
					$this->setError(JText::_('Missing argument.'));
					return false;
				}
				//$query = "SELECT * FROM $this->_tbl WHERE alias='$oid' AND scope='$scope' AND group_id='$group_id'";
				$query = "SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid) . " AND group_id=" . $this->_db->Quote($group_id);
			break;

			default:
				$query = "SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid) . " AND scope=" . $this->_db->Quote($scope);
			break;
		}
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if ($this->title == '') 
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = $this->_shorten($this->title);
		}
		$this->alias = str_replace(' ', '-', $this->alias);
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->alias));

		$this->content = trim($this->content);
		if ($this->content == '') 
		{
			$this->setError(JText::_('Please provide content.'));
			return false;
		}

		$juser = JFactory::getUser();
		if (!$this->created_by) 
		{
			$this->created_by = $juser->get('id');
		}

		if (!$this->id)
		{
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->publish_up = date('Y-m-d H:i:s', time());
		}

		if (!$this->publish_up || $this->publish_up == '0000-00-00 00:00:00') 
		{
			$this->publish_up = $this->created;
		}

		if (!$this->publish_down) 
		{
			$this->publish_down = '0000-00-00 00:00:00';
		}

		return true;
	}

	/**
	 * Shorten a string
	 * 
	 * @param      string  $text  String to shorten
	 * @param      integer $chars Length to shorten to
	 * @return     string
	 */
	public function _shorten($text, $chars=100)
	{
		$text = strip_tags($text);
		$text = trim($text);
		if (strlen($text) > $chars) 
		{
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
			$text = substr($text, 0, strrpos($text,' '));
		}
		return $text;
	}

	/**
	 * Return a count of entries based off of filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getEntriesCount($filters=array())
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) " . $this->_buildAdminQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get entries based off of filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getEntries($filters=array())
	{
		$bc = new BlogTableComment($this->_db);

		$query = "SELECT m.*, (SELECT COUNT(*) FROM " . $bc->getTableName() . " AS c WHERE c.entry_id=m.id) AS comments, u.name " . $this->_buildAdminQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildAdminQuery($filters)
	{
		$nullDate = $this->_db->getNullDate();
		//$date =& JFactory::getDate();
		$now = date('Y-m-d H:i:s', time()); //$date->toMySQL();

		$query  = "FROM $this->_tbl AS m,
					#__xprofiles AS u  
					WHERE m.scope=" . $this->_db->Quote($filters['scope']) . " AND m.created_by=u.uidNumber ";
		if (isset($filters['created_by']) && $filters['created_by'] != 0) 
		{
			$query .= " AND m.created_by=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0) 
		{
			$query .= " AND m.group_id=" . $this->_db->Quote($filters['group_id']);
		}
		if (isset($filters['scope']) && $filters['scope'] != '') 
		{
			$query .= " AND m.scope=" . $this->_db->Quote($filters['scope']);
		}
		if (isset($filters['state']) && $filters['state'] != '') 
		{
			switch ($filters['state'])
			{
				case 'public':
					$query .= " AND m.state=1";
				break;
				case 'registered':
					$query .= " AND m.state>0";
				break;
				case 'private':
					$query .= " AND m.state=0";
				break;
				case 'all':
					$query .= " AND m.state>=0";
				break;
				case 'trashed':
					$query .= " AND m.state<0";
				break;
			}
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$filters['search'] = strtolower(stripslashes($filters['search']));
			$query .= " AND (LOWER(m.title) LIKE '%" . $this->_db->getEscaped($filters['search']) . "%' OR LOWER(m.content) LIKE '%" . $this->_db->getEscaped($filters['search']) . "%')";
		}
		if (isset($filters['order']) && $filters['order'] != '') 
		{
			$query .= " ORDER BY " . $filters['order'];
		} 
		else 
		{
			$query .= " ORDER BY publish_up DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
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
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

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
		$bc = new BlogTableComment($this->_db);

		$query = "SELECT m.*, (SELECT COUNT(*) FROM " . $bc->getTableName() . " AS c WHERE c.entry_id=m.id) AS comments, u.name " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters passed
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters)
	{
		$nullDate = $this->_db->getNullDate();
		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$query  = "FROM $this->_tbl AS m,
					#__xprofiles AS u  
					WHERE m.scope='" . $this->_db->getEscaped($filters['scope']) . "' AND m.created_by=u.uidNumber ";

		if (isset($filters['year']) && $filters['year'] != 0) 
		{
			if (isset($filters['month']) && $filters['month'] != 0) 
			{
				$startmonth = $filters['year'] . '-' . $filters['month'] . '-01 00:00:00';

				if ($filters['month']+1 == 13) 
				{
					$year = $filters['year'] + 1;
					$month = 1;
				} 
				else 
				{
					$month = ($filters['month']+1);
					$year = $filters['year'];
				}
				$endmonth = sprintf("%4d-%02d-%02d 00:00:00", $year, $month,1);

				$query .= " AND m.publish_up >= " . $this->_db->Quote($startmonth) . " AND m.publish_up < " . $this->_db->Quote($endmonth) . " ";
			} 
			else 
			{
				$startyear = $filters['year'] . '-01-01 00:00:00';
				$endyear = ($filters['year']+1) . '-01-01 00:00:00';

				$query .= " AND m.publish_up >= " . $this->_db->Quote($startyear) . " AND m.publish_up < " . $this->_db->Quote($endyear) . " ";
			}
		} 
		else 
		{
			$query .= "AND (m.publish_up = " . $this->_db->Quote($nullDate) . " OR m.publish_up <= " . $this->_db->Quote($now) . ")";
					//AND (m.publish_down = " . $this->_db->Quote($nullDate) . " OR m.publish_down >= " . $this->_db->Quote($now) . ")";
		}
		if (!isset($filters['authorized']) || !$filters['authorized'])
		{
			$query .= "AND (m.publish_down = " . $this->_db->Quote($nullDate) . " OR m.publish_down >= " . $this->_db->Quote($now) . ")";
		}
		else if (isset($filters['authorized']) && $filters['authorized'] && is_numeric($filters['authorized']))
		{
			$query .= "AND ((m.publish_down = " . $this->_db->Quote($nullDate) . " OR m.publish_down >= " . $this->_db->Quote($now) . ") OR m.created_by=" . $this->_db->Quote($filters['authorized']) . ")";
		}

		if (isset($filters['created_by']) && (int) $filters['created_by'] != 0) 
		{
			$query .= " AND m.created_by=" . $this->_db->Quote(intval($filters['created_by']));
		}
		if (isset($filters['group_id']) && (int) $filters['group_id'] != 0) 
		{
			$query .= " AND m.group_id=" . $this->_db->Quote(intval($filters['group_id']));
		}
		if (isset($filters['state']) && $filters['state'] != '') 
		{
			switch ($filters['state'])
			{
				case 'all':
					$query .= " AND m.state>=0";
				break;
				case 'registered':
					$query .= " AND m.state>0";
				break;
				case 'private':
					$query .= " AND m.state=0";
				break;
				case 'public':
				default:
					$query .= " AND m.state=1 "; // AND u.public=1 ";
				break;
			}
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$filters['search'] = $this->_db->getEscaped(strtolower(stripslashes($filters['search'])));
			$query .= " AND (LOWER(m.title) LIKE '%" . $filters['search'] . "%' OR LOWER(m.content) LIKE '%" . $filters['search'] . "%')";
		}
		if (isset($filters['order']) && $filters['order'] != '') 
		{
			$query .= " ORDER BY " . $filters['order'];
		} 
		else 
		{
			$query .= " ORDER BY publish_up DESC";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		return $query;
	}

	/**
	 * Delete comments associated with an entry
	 * 
	 * @param      integer $id Blog entry
	 * @return     boolean True if comments deleted
	 */
	public function deleteComments($id=null)
	{
		if (!$id) 
		{
			$id = $this->id;
		}
		if (!$id) 
		{
			$this->setError(JText::_('Missing Entry ID.'));
			return false;
		}

		$bc = new BlogTableComment($this->_db);

		$this->_db->setQuery("DELETE FROM " . $bc->getTableName() . " WHERE entry_id=" . $this->_db->Quote($id));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		} 
		else 
		{
			return true;
		}
	}

	/**
	 * Delete files associated with an entry
	 * 
	 * @param      integer $id Blog entry
	 * @return     boolean True if files deleted
	 */
	public function deleteFiles($id=null)
	{
		// Build the file path
		/*$path = JPATH_ROOT;
		$config = $this->config;
		if (substr($config->get('uploadpath'), 0, 1) != DS) 
		{
			$path .= DS;
		}
		$path .= $config->get('uploadpath') . DS . $member->get('uidNumber');

		if (is_dir($path)) 
		{ 
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFolder::delete($path)) 
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_DIRECTORY'));
				return false;
			}
		}*/
		return true;
	}

	/**
	 * Delete tags associated with an entry
	 * 
	 * @param      integer $id Blog entry
	 * @return     boolean True if files deleted
	 */
	public function deleteTags($id=null)
	{
		if (!$id) 
		{
			$id = $this->id;
		}
		if (!$id) 
		{
			$this->setError(JText::_('Missing Entry ID.'));
			return false;
		}

		$bt = new BlogModelTags($id);
		if (!$bt->removeAll()) 
		{
			$this->setError(JText::_('UNABLE_TO_DELETE_TAGS'));
			return false;
		}
		return true;
	}

	/**
	 * Get a list of entries based on comment count
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     object Return description (if any) ...
	 */
	public function getPopularEntries($filters=array())
	{
		$filters['order'] = 'hits DESC';

		$bc = new BlogTableComment($this->_db);

		$query = "SELECT m.*, 
				(SELECT COUNT(*) FROM " . $bc->getTableName() . " AS c WHERE c.entry_id=m.id) AS comments, u.name " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of entries based on date published
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecentEntries($filters=array())
	{
		$filters['order'] = 'publish_up DESC';

		$bc = new BlogTableComment($this->_db);

		$query = "SELECT m.*, 
				(SELECT COUNT(*) FROM " . $bc->getTableName() . " AS c WHERE c.entry_id=m.id) AS comments, u.name " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the date of the first entry
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string
	 */
	public function getDateOfFirstEntry($filters=array())
	{
		$filters['order'] = 'publish_up ASC';
		$filters['limit'] = 1;
		$filters['start'] = 0;
		$filters['year']  = 0;
		$filters['month'] = 0;

		$query = "SELECT publish_up " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

