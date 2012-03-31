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
 * Short description for 'ResourcesType'
 * 
 * Long description (if any) ...
 */
class ResourcesSponsor extends JTable
{
	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       		= NULL;  // @var int(11) Primary key
	
	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias	  		= NULL;	 // @var varchar(100)

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $title     		= NULL;  // @var varchar(250)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state 		= NULL;  // @var int(3)

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $created = NULL;  // @var text
	
	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $created_by = NULL;  // @var text
	
	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $modified = NULL;  // @var text
	
	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $modified_by = NULL;  // @var text

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description 	= NULL;  // @var text

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_sponsors', 'id', $db);
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
		$this->title = trim($this->title);
		$this->description = trim($this->description);
		
		if (!$this->title) 
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}
		
		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);
		
		$juser =& JFactory::getUser();
		if (!$this->id) 
		{
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->created_by = $juser->get('id');
		} 
		else 
		{
			$this->modified = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->modified_by = $juser->get('id');
		}
		
		return true;
	}
	
	/**
	 * Short description for 'loadAlias'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
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
		
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias='$oid'");
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
	 * Short description for 'getAllCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		if (isset($filters['state']))
		{
			$query .= " WHERE state=" . intval($filters['state']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getAllTypes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array())
	{
		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
		}
		
		$query  = "SELECT * FROM $this->_tbl";
		if (isset($filters['state']))
		{
			$query .= " WHERE state=" . intval($filters['state']);
		}
		$query .= " ORDER BY ".$filters['sort']." ".$filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'])
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

