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
 * Short description for 'MembersAssociation'
 * 
 * Long description (if any) ...
 */
class MembersAssociation extends JTable
{

	/**
	 * Description for 'subtable'
	 * 
	 * @var unknown
	 */
	var $subtable = NULL;  // @var varchar(50) Primary Key

	/**
	 * Description for 'subid'
	 * 
	 * @var unknown
	 */
	var $subid    = NULL;  // @var int(11) Primary Key

	/**
	 * Description for 'authorid'
	 * 
	 * @var unknown
	 */
	var $authorid = NULL;  // @var int(11) Primary Key

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
	var $ordering = NULL;  // @var int(11)

	/**
	 * Description for 'role'
	 * 
	 * @var unknown
	 */
	var $role     = NULL;  // @var varchar(50)

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
		parent::__construct( '#__author_assoc', 'authorid', $db );
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
		if (!$this->authorid) {
			$this->setError( JText::_('Must have an author ID.') );
			return false;
		}

		if (!$this->subid) {
			$this->setError( JText::_('Must have an item ID.') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'deleteAssociations'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteAssociations( $id=NULL )
	{
		if (!$id) {
			$id = $this->authorid;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE authorid='".$id."'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}

