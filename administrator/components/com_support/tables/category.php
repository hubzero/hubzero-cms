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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------


/**
 * Short description for 'SupportCategory'
 * 
 * Long description (if any) ...
 */
class SupportCategory extends JTable
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
	var $category = NULL;  // @var varchar(50)


	/**
	 * Description for 'section'
	 * 
	 * @var unknown
	 */
	var $section  = NULL;  // @var int(11)

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
		parent::__construct( '#__support_categories', 'id', $db );
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
		if (trim( $this->category ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getCategories'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $section Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCategories( $section=NULL )
	{
		if ($section !== NULL) {
			$section = ($section) ? $section : 1;
			$where = "WHERE section=$section";
		} else {
			$where = "";
		}

		$this->_db->setQuery( "SELECT category AS id, category AS txt FROM $this->_tbl $where ORDER BY category");
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array() )
	{
		$query = " FROM $this->_tbl AS c, #__support_sections AS s"
				. " WHERE c.section=s.id";
		if (isset($filters['order']) && $filters['order'] != '') {
			$query .= " ORDER BY ".$filters['order'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
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
	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
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
	public function getRecords( $filters=array() )
	{
		$filters['order'] = 'section, category';

		$query  = "SELECT c.id, c.category, s.section";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

