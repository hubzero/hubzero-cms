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
 * Table class for publication category
 */
class PublicationCategory extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;
	
	/**
	 * Category title displayed to the user
	 * 
	 * @var string
	 */	
	var $name       		= NULL;
	
	/**
	 * Alias referenced in code instead of the ID (e.g. tool) - for developers
	 * 
	 * @var string
	 */	
	var $alias       		= NULL;
	
	/**
	 * URL alias, varchar(200)
	 * 
	 * @var string
	 */	
	var $url_alias       	= NULL;
	
	/**
	 * DOI resourceType
	 * 
	 * @var string
	 */	
	var $dc_type       		= NULL;
	
	/**
	 * Description
	 * 
	 * @var text
	 */	
	var $description       	= NULL;
	
	/**
	 * Offer as category choice in publication contributions
	 * 
	 * @var int
	 */	
	var $contributable      = NULL;
	
	/**
	 * Category state (published/unpublished)
	 * 
	 * @var int
	 */	
	var $state      		= NULL;
	
	/**
	 * Customizable metadata areas
	 * 
	 * @var text
	 */	
	var $customFields      = NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */	
	var $params      = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_categories', 'id', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check() 
	{
		if (trim( $this->name ) == '') 
		{
			$this->setError( JText::_('Your publication category name must contain text.') );
			return false;
		}
		if (trim( $this->alias ) == '') 
		{
			$this->setError( JText::_('Your publication category alias must contain text.') );
			return false;
		}
		if (trim( $this->url_alias ) == '') 
		{
			$this->setError( JText::_('Your publication url alias name must contain text.') );
			return false;
		}
		return true;
	}
		
	/**
	 * Get record count
	 * 
	 * @param      array 		$filters
	 * @return     integer
	 */	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		$query .= isset($filters['state']) && $filters['state'] == 'all' ? '' : " WHERE state=1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get records
	 * 
	 * @param      array 		$filters
	 * @return     object
	 */	
	public function getCategories( $filters = array() ) 
	{
		$query  = "SELECT * ";
		
		if (isset($filters['itemCount']) && $filters['itemCount'] == 1)
		{
			$query .= ", (SELECT COUNT(*) FROM #__publications as P 
						JOIN #__publication_versions as V ON V.publication_id = P.id
						AND V.main=1 WHERE P.category = C.id AND V.state=1) AS itemCount ";
		}
		$query .= " FROM $this->_tbl as C ";
		
		if (isset($filters['state']) && $filters['state'] == 'all')
		{
			// don't limit by state
		}
		else
		{
			$query .= isset($filters['state']) && intval($filters['state']) > 0 ? 'WHERE C.state=' . $filters['state'] : " WHERE C.state=1 ";			
		}
		
		$orderby = isset($filters['sort']) ? $filters['sort'] : "name";
		$order_dir = isset($filters['sort_Dir']) ? $filters['sort_Dir'] : "ASC";
		$query .= " ORDER BY C.".$orderby." ".$order_dir." ";
		
		if (isset($filters['start']) && isset($filters['limit']))
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get record by alias name
	 * 
	 * @param      string 		$alias
	 * @return     object
	 */	
	public function getCategory( $alias = '' ) 
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
	 * Get record ID by alias name
	 * 
	 * @param      string 		$alias
	 * @return     integer or NULL
	 */	
	public function getCatId ( $alias='' ) 
	{
		if (!$alias) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE alias='".$alias."' LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get contributable categories
	 * 
	 * @return     object
	 */	
	public function getContribCategories() 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE contributable=1 ORDER BY name" );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Check usage by category
	 * 
	 * @param      integer		$id
	 * @return     integer or NULL
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
		
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' 
			. DS . 'com_publications' . DS . 'tables' . DS . 'publication.php' );
		
		$p = new Publication( $this->_db );
		
		$this->_db->setQuery( "SELECT count(*) FROM $p->_tbl WHERE category=" . $id);
		return $this->_db->loadResult();
	}
	
	/**
	 * Suggest category
	 * 
	 * @param      array		$primary
	 * @param      integer		$picked
	 * @param      integer		$multiple
	 * @return     object
	 */	
	public function suggestCat( $primary = array(), $picked = 0, $multiple = 0 ) 
	{
		$cat = 'download'; // 'download' as default type
		if (empty($primary)) 
		{
			return 0;
		}
		if ($primary[0] == 'app') 
		{
			return 'tool'; // tool type
		}
		elseif ($primary[0] == 'file' && isset($primary[1])) 
		{
			// Consider file extention
			$ext = explode('.',$primary[1]);
			$ext = end($ext);
			$ext = strtolower($ext);
			switch ( $ext ) 
			{
				default:
				case 'pdf': 
					$cat = $multiple ? array('download') : 'download'; 
					break;				
			}
		}
		
		return $cat; 
	}
}
