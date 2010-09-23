<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
