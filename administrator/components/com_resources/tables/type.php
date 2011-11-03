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
 * Short description for 'ResourcesType'
 * 
 * Long description (if any) ...
 */
class ResourcesType extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       		= NULL;  // @var int(11) Primary key
	var $alias	  		= NULL;	 // @var varchar(100)

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type     		= NULL;  // @var varchar(250)

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category		= NULL;  // @var int(11)

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description 	= NULL;  // @var text

	/**
	 * Description for 'contributable'
	 * 
	 * @var unknown
	 */
	var $contributable 	= NULL;  // @var int(2)

	/**
	 * Description for 'customFields'
	 * 
	 * @var unknown
	 */
	var $customFields 	= NULL;  // @var text

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params 		= NULL;  // @var text

	//-----------

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
		parent::__construct( '#__resource_types', 'id', $db );
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
		if (trim( $this->type ) == '') {
			$this->setError( JText::_('Your resource type must contain text.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getMajorTypes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     integer Return description (if any) ...
	 */
	public function getMajorTypes()
	{
		return $this->getTypes( 27 );
	}

	/**
	 * Short description for 'getAllCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getAllCount( $filters=array() )
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		if (isset($filters['category']) && $filters['category'] != 0) {
			$query .= " WHERE category=".$filters['category'];
		}

		$this->_db->setQuery( $query );
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
	public function getAllTypes( $filters=array() )
	{
		$query  = "SELECT * FROM $this->_tbl ";
		if (isset($filters['category']) && $filters['category'] != 0) {
			$query .= "WHERE category=".$filters['category']." ";
		}
		$query .= "ORDER BY ".$filters['sort']." ".$filters['sort_Dir']." ";
		$query .= "LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getTypes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $cat Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getTypes( $cat='0' )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE category=".$cat." ORDER BY type" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'checkUsage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function checkUsage( $id=NULL )
	{
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );

		$r = new ResourcesResource( $this->_db );

		$this->_db->setQuery( "SELECT count(*) FROM $r->_tbl WHERE type=".$id." OR logical_type=".$id );
		return $this->_db->loadResult();
	}
}

