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
 * Short description for 'CitationsType'
 * 
 * Long description (if any) ...
 */
class CitationsType extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id    			= NULL;

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type			= NULL;

	/**
	 * Description for 'type_title'
	 * 
	 * @var unknown
	 */
	var $type_title		= NULL;

	/**
	 * Description for 'type_desc'
	 * 
	 * @var unknown
	 */
	var $type_desc 		= NULL;

	/**
	 * Description for 'type_export'
	 * 
	 * @var unknown
	 */
	var $type_export  	= NULL;
	
	/**
	 * Description for 'fields'
	 * 
	 * @var unknown
	 */
	var $fields  	= NULL;

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
		parent::__construct( '#__citations_types', 'id', $db );
	}

	/**
	 * Short description for 'getType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getType( $id = "" )
	{
		$where = ($id != "") ? "WHERE id='{$id}'" : "";

		$sql = "SELECT * FROM {$this->_tbl} {$where} ORDER BY type";
		$this->_db->setQuery( $sql );
		$results = $this->_db->loadAssocList();

		return $results;
	}
}

