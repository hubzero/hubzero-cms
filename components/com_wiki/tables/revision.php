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
 * Wiki table class for page version
 */
class WikiPageRevision extends JTable
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
	var $pageid     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $version    = NULL;

	/**
	 * datetime
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
	 * int(1)
	 * 
	 * @var integer
	 */
	var $minor_edit = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $pagetext   = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $pagehtml   = NULL;

	/**
	 * int(1)
	 * 
	 * @var integer
	 */
	var $approved   = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $summary    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $length     = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_version', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->minor_edit = intval($this->minor_edit);
		$this->approved = intval($this->approved);
		$this->version = intval($this->version);
		if ($this->version <= 0)
		{
			$this->version = 1;
		}

		$this->pageid = intval($this->pageid);
		if (!$this->pageid) 
		{
			$this->setError(JText::_('This revision is missing its page ID.'));
			return false;
		}
		if (trim($this->pagetext) == '') 
		{
			$this->setError(JText::_('Please provide content. A wiki page cannot be empty.'));
			return false;
		}
		if (!$this->id)
		{
			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}
		$this->length = strlen($this->pagetext);

		return true;
	}

	/**
	 * Load a record by the page/version combination and bind to $this
	 * 
	 * @param      integer $pageid  Page ID
	 * @param      integer $version Version number
	 * @return     boolean True on success
	 */
	public function loadByVersion($pageid, $version=0)
	{
		if (!$pageid) 
		{
			return;
		}
		if ($version) 
		{
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND version='$version'");
		} 
		else 
		{
			$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC LIMIT 1");
		}
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
	 * Get a list of all contributors on a wiki page
	 * 
	 * @return     array
	 */
	public function getContributors()
	{
		$this->_db->setQuery("SELECT DISTINCT created_by AS id FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'");
		$contributors = $this->_db->loadObjectList();

		$cons = array();
		if (count($contributors) > 0) 
		{
			foreach ($contributors as $con)
			{
				$cons[] = $con->id;
			}
		}
		return $cons;
	}

	/**
	 * Get a count of all revisions for a page
	 * 
	 * @return     integer
	 */
	public function getRevisionCount()
	{
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'");
		return $this->_db->loadResult();
	}

	/**
	 * Get all the revision numbers for a page
	 * 
	 * @param      integer $pageid Page ID
	 * @return     array
	 */
	public function getRevisionNumbers($pageid=NULL)
	{
		if (!$pageid) 
		{
			$pageid = $this->pageid;
		}
		$this->_db->setQuery("SELECT DISTINCT version FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all the revisions for a page
	 * 
	 * @param      integer $pageid Page ID
	 * @return     array
	 */
	public function getRevisions($pageid=NULL)
	{
		if (!$pageid) 
		{
			$pageid = $this->pageid;
		}
		return $this->getRecords(array('pageid' => $pageid));
	}

	/**
	 * Get a record count based off of filters passed
	 * 
	 * @param      array $filters Filters to build from
	 * @return     integer
	 */
	public function getRecordsCount($filters=array())
	{
		$sql  = "SELECT COUNT(*) ";
		$sql .= $this->buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records based off of filters passed
	 * 
	 * @param      array $filters Filters to build from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$sql  = "SELECT r.id, r.pageid, r.version, r.created, r.created_by, r.minor_edit, r.approved, r.summary, r.length, u.name AS created_by_name, u.username AS created_by_alias ";
		$sql .= $this->buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build an SQL statement based on filters passed
	 * 
	 * @param      array $filters Filters to build from
	 * @return     string SQL
	 */
	public function buildQuery($filters)
	{
		$query = " FROM $this->_tbl AS r,
					#__users AS u 
					WHERE r.created_by=u.id AND r.pageid='" . (int) $filters['pageid'] . "'";
		if (isset($filters['search']) && $filters['search']) 
		{
			$query .= " AND LOWER(r.pagehtml) LIKE '%" . strtolower($filters['search']) . "%'";
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '') 
		{
			$query .= " ORDER BY " . $filters['sortby'];
		} 
		else 
		{
			$query .= " ORDER BY version DESC, created DESC";
		}

		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') 
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}
}

