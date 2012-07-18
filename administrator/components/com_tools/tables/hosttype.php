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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hosttype'
 * 
 * Long description (if any) ...
 */
class MwHosttype extends JTable
{

	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name;

	/**
	 * Description for 'value'
	 * 
	 * @var unknown
	 */
	var $value;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description;

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
		parent::__construct('hosttype', 'name', $db);
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

		if (!$this->name) 
		{
			$this->setError( JText::_('No name provided') );
			return false;
		}

		if (!$this->description) 
		{
			$this->setError( JText::_('No description provided.') );
			return false;
		}

		if (!$this->value) 
		{
			$this->setError( JText::_('No value provided.') );
			return false;
		}

		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	public function store($insert=null, $updateNulls=false)
	{
		$k = $this->_tbl_key;

		if ($insert)
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		else 
		{
			if( $this->$k)
			{
				$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
			}
			else
			{
				$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
			}
		}
		if( !$ret )
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Default delete method
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	public function delete( $oid=null )
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = $oid;
		}

		$query = 'DELETE FROM '.$this->_db->nameQuote( $this->_tbl ).
				' WHERE '.$this->_tbl_key.' = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _buildQuery($filters=array())
	{
		$where = array();

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.name) LIKE '%" . strtolower($filters['search']) . "%' OR LOWER(c.description) LIKE '%" . strtolower($filters['search']) . "%')";
		}

		$query = "FROM $this->_tbl AS c";
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
		$query  = "SELECT c.* " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'name';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
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
