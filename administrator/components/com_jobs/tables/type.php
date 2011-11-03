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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'JobType'
 * 
 * Long description (if any) ...
 */
class JobType extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category = NULL;  // @var varchar(150)

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
		parent::__construct( '#__jobs_types', 'id', $db );
	}

	/**
	 * Short description for 'getTypes'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $sortby Parameter description (if any) ...
	 * @param      string $sortdir Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function getTypes($sortby = 'id', $sortdir = 'ASC')
	{
		$types = array();

		$query  = "SELECT id, category ";
		$query .= "FROM #__jobs_types ORDER BY $sortby $sortdir ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) {
			foreach ($result as $r)
			{
				$types[$r->id] = $r->category;
			}
		}

		return $types;
	}

	/**
	 * Short description for 'getType'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $id Parameter description (if any) ...
	 * @param      string $default Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getType($id = NULL, $default = 'unspecified')
	{
		if ($id === NULL) {
			 return false;
		}
		if ($id == 0 ) {
			return $default;
		}

		$query  = "SELECT category ";
		$query .= "FROM #__jobs_types WHERE id='".$id."'  ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

