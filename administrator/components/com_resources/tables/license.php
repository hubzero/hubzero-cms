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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ToolLicense'
 * 
 * Long description (if any) ...
 */
class ResourcesLicense extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id  = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name     = NULL;  // @var varchar(250)


	/**
	 * Description for 'text'
	 * 
	 * @var unknown
	 */
	var $text = NULL;  // @var int(11)


	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title = NULL;

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
	var $ordering = NULL;
	
	var $apps_only 	= NULL;
	var $main 		= NULL;
	var $icon 		= NULL;
	var $url 		= NULL;
	var $agreement  = NULL;
	var $info  		= NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct('#__resource_licenses', 'id', $db);
	}
	
	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function load($oid=NULL)
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
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		$this->text = trim($this->text);
		
		if (!$this->text) 
		{
			$this->setError(JText::_('Please provide some text.'));
			return false;
		}
		
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
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function _buildQuery($filters=array())
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
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array())
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
}

