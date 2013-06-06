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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for publication license
 */
class PublicationLicense extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id  			= NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $name     		= NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $text 			= NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $title 			= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering 		= NULL;

	/**
	 * tinyint(3)
	 * 
	 * @var integer
	 */
	var $apps_only 		= NULL;

	/**
	 * int(11)
	 * 
	 * @var string
	 */
	var $main 			= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $icon 			= NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $url 			= NULL;

	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $agreement  	= NULL;

	/**
	 * text
	 * 
	 * @var text
	 */
	var $info  			= NULL;
	
	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $active  		= NULL;
	
	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $customizable  	= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_licenses', 'id', $db );
	}
	
	/**
	 * Load a record and bind to $this
	 * 
	 * @param      mixed $oid Integer or string (alias)
	 * @return     mixed False if error, Object on success
	 */
	public function load($oid = NULL)
	{
		if ($oid === NULL) 
		{
			return false;
		}
		
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}
		
		$oid = trim($oid);
		
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE name='$oid'");
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
		$this->text = trim($this->text);
				
		if (!$this->title) 
		{
			$this->title = substr($this->text, 0, 70);
			if (strlen($this->title >= 70)) 
			{
				$this->title .= '...';
			}
		}
		
		if (!$this->name)
		{
			$this->name = $this->title;
		}
		$this->name = str_replace(' ', '-', strtolower($this->name));
		$this->name = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->name);
		
		return true;
	}
		
	/**
	 * Build a query from filters
	 * 
	 * @param      array   $filters Filters to build query from
	 * @return     string SQL
	 */	
	protected function _buildQuery($filters = array())
	{
		$query  = "FROM $this->_tbl AS c";

		$where = array();
		if (isset($filters['state'])) 
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.title) LIKE '%" . strtolower($filters['search']) . "%' 
				OR LOWER(c.`text`) LIKE '%" . strtolower($filters['search']) . "%')";
		}
		
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}
	
	/**
	 * Get record counts
	 * 
	 * @param      array   $filters Filters to build query from
	 * @return     array
	 */
	public function getCount($filters = array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	/**
	 * Get records
	 * 
	 * @param      array   $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters = array())
	{
		$query  = "SELECT c.*";
		$query .= $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get licenses
	 * 
	 * @param      array   $filters Filters to build query from
	 * @return     object
	 */	
	public function getLicenses ( $filters = array() ) 
	{		
		$apps_only = isset($filters['apps_only']) ? $filters['apps_only'] : '';
		$sortby  = isset($filters['sortby']) && $filters['sortby'] != '' ? $filters['sortby'] : 'ordering';
		
		$query = "SELECT * FROM $this->_tbl ";
		$query.= $apps_only ? " WHERE active=1 " : " WHERE (apps_only=".$apps_only." OR main=1) AND active=1";
		$query.= " ORDER BY ".$sortby;
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get default license
	 * 
	 * @return     object
	 */	
	public function getDefaultLicense() 
	{	
		$query = "SELECT * FROM $this->_tbl ";
		$query.= " WHERE main=1 ";
		$query.= " LIMIT 1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get license by id
	 * 
	 * @param      integer $id License ID
	 * @return     mixed False if error, Object on success
	 */	
	public function getLicense ( $id = 0 ) 
	{
		if (!$id) 
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl ";
		$query.= " WHERE id='$id'";
		$query.= " LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Load by ordering
	 * 
	 * @param      mixed $ordering Integer or string (alias)
	 * @return     mixed False if error, Object on success
	 */
	public function loadByOrder($ordering = NULL)
	{
		if ($ordering === NULL) 
		{
			return false;
		}
				
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE ordering='$ordering' LIMIT 1");
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
	 * Change order
	 * 
	 * @param      integer $dir 
	 * @return     mixed False if error, Object on success
	 */	
	public function changeOrder ( $dir ) 
	{
		$newOrder = $this->ordering + $dir;
		
		// Load record in prev position
		$old = new PublicationLicense( $this->_db );
		if ($old->loadByOrder($newOrder))
		{
			$old->ordering  = $this->ordering;
			$old->store();
		}
		
		$this->ordering = $newOrder;
		$this->store();

		return true;
	}
	
	/**
	 * Get license by name
	 * 
	 * @param      string $name License name
	 * @return     mixed False if error, Object on success
	 */	
	public function getLicenseByName ( $name = '' ) 
	{
		if (!$name) 
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl ";
		$query.= " WHERE name LIKE '$name'";
		$query.= " LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Get license by pub version id
	 * 
	 * @param      integer $vid Pub version ID
	 * @return     mixed False if error, Object on success
	 */	
	public function getPubLicense ( $vid = null ) 
	{		
		if (!$vid) 
		{
			return false;
		}
				
		$query = "SELECT L.*, v.license_type, v.license_text FROM $this->_tbl AS L, #__publication_versions AS v  ";
		$query.= " WHERE v.id=".$vid." AND v.license_type=L.id";
		$query.= " LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Make license  not default
	 * 
	 * @param      integer $except License ID to exclude
	 * @return     boolean
	 */	
	public function undefault ( $except = 0 ) 
	{						
		$query = "UPDATE $this->_tbl SET main=0 ";
		$query.= $except ? " WHERE id != ".$except : '';
		$this->_db->setQuery( $query );
		if ($this->_db->query()) 
		{
			return true;
		}
		return false;
	}
}
