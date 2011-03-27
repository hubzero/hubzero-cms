<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class Hubzero_Message_Action extends JTable
{
	var $id          = NULL;  // @var int(11) Primary key
	var $class       = NULL;  // @var varchar(20)
	var $element     = NULL;  // @var int(11)
	var $description = NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_action', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->element ) == '') {
			$this->setError( JText::_('Please provide an element.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getActionItems( $type=null, $component=null, $element=null, $uid=null ) 
	{
		if (!$uid) {
			return false;
		}
		if (!$type) {
			return false;
		}
		if (!$component) {
			$component = $this->class;
		}
		if (!$component) {
			return false;
		}
		if (!$element) {
			$element = $this->element;
		}
		if (!$element) {
			return false;
		}
		
		$query = "SELECT m.id 
				FROM #__xmessage_recipient AS r, $this->_tbl AS a, #__xmessage AS m
				WHERE m.id=r.mid AND r.actionid = a.id AND m.type='$type' AND r.uid='$uid' AND a.class='$component' AND a.element='$element'";

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}

