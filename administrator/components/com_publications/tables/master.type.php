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
 * Table class for publication master type
 */
class PublicationMasterType extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       			= NULL;
		
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $type				= NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $alias				= NULL;
	
	/**
	 * Text
	 * 
	 * @var text
	 */
	var $description		= NULL;
	
	/**
	 * Offer as category choice in publication primary content contributions
	 * 
	 * @var int
	 */	
	var $contributable      = NULL;
		
	/**
	 * Offer as category choice in publication supporting docs
	 * 
	 * @var int
	 */	
	var $supporting     	= NULL;
	
	/**
	 * Ordering
	 * 
	 * @var int
	 */	
	var $ordering     		= NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */	
	var $params      		= NULL;
		
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_master_types', 'id', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */	
	public function check() 
	{
		if (trim( $this->type ) == '') 
		{
			$this->setError( JText::_('Your publication master type must contain text.') );
			return false;
		}
		if (trim( $this->alias ) == '') 
		{
			$this->setError( JText::_('Your publication master type alias must contain text.') );
			return false;
		}
		return true;
	}
		
	/**
	 * Get record by alias name
	 * 
	 * @param      string 		$alias
	 * @return     object or false
	 */	
	public function getType( $alias='' ) 
	{
		if (!$alias) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE alias='".$alias."' LIMIT 1" );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}
	
	/**
	 * Get record id by alias name
	 * 
	 * @param      string 		$alias
	 * @return     integer
	 */	
	public function getTypeId( $alias='' ) 
	{
		if (!$alias) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE alias='".$alias."' LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get record alias by id
	 * 
	 * @param      integer 		$id
	 * @return     integer
	 */	
	public function getTypeAlias( $id='' ) 
	{
		if (!$id) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT alias FROM $this->_tbl WHERE id='".$id."' LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get records
	 * 
	 * @param      string  $select 				Select query
	 * @param      integer $contributable		Contributable?
	 * @param      integer $supporting 			Supporting?
	 * @param      string  $orderby 			Order by
	 * @param      string  $config
	 * @return     array
	 */	
	public function getTypes( $select = '*', $contributable = 0, $supporting = 0, $orderby = 'id', $config = '') 
	{
		$query  = "SELECT $select FROM $this->_tbl ";
		if ($contributable) 
		{
			$query .= "WHERE contributable=1 ";
		}
		elseif ($supporting) 
		{
			$query .= "WHERE supporting=1 ";
		}
		
		if (!JPluginHelper::isEnabled('projects', 'apps')) 
		{
			$query .= " AND alias!='apps' ";	
		}
	
		$query .= "ORDER BY ".$orderby;
		
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		if ($select == 'alias') 
		{
			$types = array();
			if ($results) 
			{
				foreach($results as $result)
				{
					$types[] = $result->alias;
				}
			}
			return $types;
		}
		return $results;
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
			$filters['sort'] = 'id';
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
	 * Build a query from filters
	 * 
	 * @param      array   $filters Filters to build query from
	 * @return     string SQL
	 */	
	protected function _buildQuery($filters = array())
	{
		$query  = "FROM $this->_tbl AS c";

		$where = array();
		
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}
	
	/**
	 * Check type usage
	 * 
	 * @param      integer 		$id		type id
	 * @return     integer
	 */	
	public function checkUsage( $id = NULL ) 
	{
		if (!$id) 
		{
			$id = $this->id;
		}
		if (!$id) 
		{
			return false;
		}
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_publications'.DS.'tables'.DS.'publication.php' );
		
		$p = new Publication( $this->_db );
		
		$this->_db->setQuery( "SELECT count(*) FROM $p->_tbl WHERE master_type=".$id);
		return $this->_db->loadResult();
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
		$old = new PublicationMasterType( $this->_db );
		
		if ($old->loadByOrder($newOrder))
		{
			$old->ordering  = $this->ordering;
			$old->store();
		} 		
		
		$this->ordering = $newOrder;
		$this->store();

		return true;
	}
}
