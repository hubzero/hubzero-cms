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
 * Table class for wishlist
 */
class Wishlist extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         	= NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $category       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $referenceid	= NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $description	= NULL;

	/**
	 * varchar(150)
	 * 
	 * @var string
	 */
	var $title			= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created    	= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by 	= NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $state     		= NULL;

	/**
	 * int(3)  // can any user view and submit to it?
	 * 
	 * @var integer
	 */
	var $public			= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist', 'id', $db);
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
			$this->setError(JText::_('Missing title for the wish list'));
			return false;
		}

		return true;
	}

	/**
	 * Get the ID of an entry
	 * 
	 * @param      integer $rid Reference ID
	 * @param      string  $cat Category
	 * @return     mixed False if errors, integer on success
	 */
	public function get_wishlistID($rid=0, $cat='resource')
	{
		if ($rid === NULL) 
		{
			$rid = $this->referenceid;
		}
		if ($rid === NULL) 
		{
			return false;
		}

		// get individuals
		$sql = "SELECT id"
			. "\n FROM $this->_tbl "
			. "\n WHERE referenceid='" . $rid . "' AND category='" . $cat . "' ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery($sql);
		return  $this->_db->loadResult();
	}

	/**
	 * Create an entry
	 * 
	 * @param      string  $category    Category
	 * @param      integer $refid       Reference ID
	 * @param      integer $public      Public/private list
	 * @param      string  $title       Entry title
	 * @param      string  $description Entry description
	 * @return     mixed False if errors, integer on success
	 */
	public function createlist($category='resource', $refid, $public=1, $title='', $description='')
	{
		if ($refid === NULL) 
		{
			return false;
		}

		$xhub =& Hubzero_Factory::getHub();
		$jconfig = JFactory::getConfig();
		$sitename = $jconfig->getValue('config.sitename');
		
		$juser =& JFactory::getUser();

		$this->created     = date('Y-m-d H:i:s');
		$this->category    = $category;
		$this->created_by  = $juser->get('id');
		$this->referenceid = $refid;
		$this->description = $description;
		$this->public      = $public;

		switch ($category)
		{
			case 'general':
				$this->title = $title ? $title : $sitename;

				if (!$this->store()) 
				{
					$this->_error = $this->getError();
					return false;
				} 
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;

			case 'resource':
				// resources can only have one list
				if (!$this->get_wishlist('', $refid, 'resource')) 
				{
					$this->title = $title ? $title : 'Resource #' . $rid;

					if (!$this->store()) 
					{
						$this->_error = $this->getError();
						return false;
					}
					// Checkin wishlist
					$this->checkin();

					return $this->id;
				} 
				else 
				{
					return $this->get_wishlistID($refid); // return existing id
				}
			break;

			case 'group':
				$this->title = $title ? $title : 'Group #' . $rid;
				if (!$this->store()) 
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;

			case 'user':
				$this->title = $title;
				if (!$this->store()) 
				{
					$this->_error = $this->getError();
					return false;
				}
				// Checkin wishlist
				$this->checkin();

				return $this->id;
			break;
		}

		return 0;
	}

	/**
	 * Get the title for an entry
	 * 
	 * @param      integer $id Entry ID
	 * @return     mixed False if error, string on success
	 */
	public function getTitle($id)
	{
		if ($id === NULL) 
		{
			return false;
		}
		$sql = "SELECT w.title FROM $this->_tbl AS w WHERE w.id=" . $id;

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Check if an entry is the primary wislist
	 * 
	 * @param      integer $id Entry ID
	 * @return     boolean True if primary
	 */
	public function is_primary($id)
	{
		if ($id === NULL) 
		{
			return false;
		}
		$sql = "SELECT w.* FROM $this->_tbl AS w WHERE w.id=" . $id . " AND w.referenceid=1 AND w.category='general'";

		$this->_db->setQuery($sql);
		$bingo = $this->_db->loadResult();
		if ($bingo) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Load a record
	 * 
	 * @param      integer $id          Entry ID
	 * @param      integer $refid       Reference ID
	 * @param      string  $cat         Category
	 * @param      integer $primary     Primary list?
	 * @param      integer $getversions Return reference versions?
	 * @return     mixed False if error, object on success
	 */
	public function get_wishlist($id='', $refid=0, $cat='', $primary = 0, $getversions=0)
	{
		if ($id===NULL && $refid===0 && $cat===NULL) 
		{
			return false;
		}
		if ($id && !intval($id)) 
		{
			return false;
		}
		if ($refid && !intval($refid)) 
		{
			return false;
		}

		$sql = "SELECT w.*";
		//if($cat == 'resource') {
			//$sql .= "\n , r.title as resourcetitle, r.type as resourcetype, r.alias, r.introtext";
		//}
			$sql .= "\n FROM $this->_tbl AS w";
		//if($cat == 'resource') {
			//$sql .= "\n JOIN #__resources AS r ON r.id=w.referenceid";	
		//}
		if ($id) 
		{
			$sql .=	"\n WHERE w.id=" . $id;
		} 
		else if ($refid && $cat) 
		{
			$sql .=	"\n WHERE w.referenceid=" . $refid . " AND w.category='" . $cat . "'";
		} 
		else if ($primary) 
		{
			$sql .=	"\n WHERE w.referenceid=1 AND w.category='general'";
		}

		$this->_db->setQuery($sql);
		$res = $this->_db->loadObjectList();
		$wishlist = ($res) ? $res[0] : array();

		// get parent 
		//$parent = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);

		if (count($wishlist) > 0 && $wishlist->category=='resource') 
		{
			$wishlist->resource = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
			// Currenty for tools only
			if ($getversions && $wishlist->resource && isset($wishlist->resource->type) && $wishlist->resource->type==7) 
			{
				$wishlist->resource->versions = $this->get_parent_versions($wishlist->referenceid, $wishlist->resource->type);
			}
		}

		return $wishlist;
	}

	/**
	 * Get the parent tool version
	 * 
	 * @param      integer $rid  Resource ID
	 * @param      integer $type Resource type
	 * @return     array
	 */
	public function get_parent_versions($rid, $type)
	{
		$versions = array();
		// currently for tools only
		if ($type == 7) 
		{
			$query  = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id='" . $rid . "'";
			$query .= " AND v.state=3 ";
			$query .= " OR v.state!=3 ORDER BY state DESC, revision DESC LIMIT 3";
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$versions = $result ? $result : array();
		}

		return $versions;
	}

	/**
	 * Get the parent resource for a wishlist
	 * 
	 * @param      integer $refid Resource ID
	 * @param      string  $cat   Resource type
	 * @return     array
	 */
	public function get_wishlist_parent($refid, $cat='resource')
	{
		$resource = array();
		if ($cat == 'resource') 
		{
			$sql = "SELECT r.title, r.type, r.alias, r.introtext, t.type as typetitle"
				. "\n FROM #__resources AS r"
				. "\n LEFT JOIN #__resource_types AS t ON t.id=r.type "
				. "\n WHERE r.id='" . $refid . "'";
			$this->_db->setQuery($sql);
			$res  = $this->_db->loadObjectList();
			$resource = ($res) ? $res[0]: array();
		}

		return $resource;
	}

	/**
	 * Get authors of a resource
	 * 
	 * @param      integer $refid Resource ID
	 * @return     array
	 */
	public function getCons($refid)
	{
		$sql = "SELECT n.uidNumber AS id"
			 . "\n FROM #__xprofiles AS n"
			 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
			 . "\n WHERE a.subtable = 'resources'"
			 . "\n AND a.subid=" . $refid;

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a tool's development group
	 * 
	 * @param      integer $refid  Tool ID
	 * @param      array   $groups ?
	 * @return     string
	 */
	public function getToolDevGroup($refid, $groups = array())
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= " JOIN #__tool AS t ON g.toolid=t.id ";
		$query .= " JOIN #__resources as r ON r.alias = t.toolname";
		$query .= " WHERE r.id = '" . $refid . "' AND g.role=1 ";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Build a query from filters
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$sql = "FROM $this->_tbl AS m LEFT JOIN #__users AS u ON m.created_by=u.id ";

		$w = array();
		if (isset($filters['category']) && $filters['category']) 
		{
			$w[] = "m.category=" . $filters['category'];
		}
		if (isset($filters['referenceid']) && $filters['referenceid']) 
		{
			$w[] = "m.referenceid=" . $filters['referenceid'];
		}
		if (isset($filters['state'])) 
		{
			$w[] = "m.state=" . $filters['state'];
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$w[] = "m.title LIKE '%" . $filters['search'] . "%'";
		}

		$sql .= (count($w) > 0) ? "WHERE " : "";
		$sql .= implode(" AND ", $w);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$sql .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'] != '') 
		{
			$sql .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		return $sql;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = '';
		$query = "SELECT count(*) " . $this->buildQuery($filters);

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
		$query = "SELECT m.*, u.name, (SELECT COUNT(*) FROM #__wishlist_item AS w WHERE w.wishlist = m.id) AS wishes " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

