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
 * Table class for project types
 */
class ProjectType extends JTable 
{
	
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * Type name
	 * 
	 * @var string
	 */	
	var $type      			= NULL;
	
	/**
	 * Params
	 * 
	 * @var text
	 */	
	var $params       		= NULL;
	
	/**
	 * Description, varchar(255)
	 * 
	 * @var string
	 */	
	var $description      		= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_types', 'id', $db );
	}
	
	/**
	 * Get params
	 * 
	 * @param      integer $type
	 * @return     string or NULL
	 */
	public function getParams ( $type = 1 )
	{
		$this->_db->setQuery( "SELECT params FROM $this->_tbl WHERE id=$type " );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get types
	 * 
	 * @return     object or NULL
	 */
	public function getTypes ()
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl ");
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get type title
	 * 
	 * @param      integer $id
	 * @return     string or NULL
	 */
	public function getTypeTitle ( $id = 0 )
	{
		$this->_db->setQuery( "SELECT type FROM $this->_tbl WHERE id=$id ");
		return $this->_db->loadResult();
	}
	
	/**
	 * Get ID by type title
	 * 
	 * @param      string $type
	 * @return     string or NULL
	 */
	public function getIdByTitle ( $type = '' )
	{
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE type='$type' ");
		return $this->_db->loadResult();
	}
}
