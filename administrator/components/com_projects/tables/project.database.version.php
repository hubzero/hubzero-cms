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
 * Table class for project database versions
 */
class ProjectDatabaseVersion extends JTable 
{	
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;
	
	/**
	 * Database name
	 * 
	 * @var string
	 */	
	var $database_name  	= NULL;
		
	/**
	 * Version
	 * 
	 * @var integer
	 */	
	var $version      		= NULL;	
			
	/**
	 * Data definition
	 * 
	 * @var text
	 */	
	var $data_definition  	= NULL;
		
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_database_versions', 'id', $db );
	}
	
	/**
	 * Get max version number
	 * 
	 * @param      string 		$dbname
	 * 
	 * @return     integer or NULL
	 */
	public function getMaxVersion( $dbname = '')
	{		
		if ($dbname === NULL) 
		{
		 	return false;
		}
		
		$query = "SELECT MAX(version) as version FROM $this->_tbl
		 			WHERE database_name='$dbname'";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
