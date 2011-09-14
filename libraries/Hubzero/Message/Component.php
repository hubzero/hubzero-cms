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

class Hubzero_Message_Component extends JTable
{
	var $id        = NULL;  // @var int(11) Primary key
	var $component = NULL;  // @var varchar(50)
	var $action    = NULL;  // @var varchar(100)
	var $title     = NULL;  // @var varchar(255)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_component', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->component ) == '') {
			$this->setError( JText::_('Please provide a component.') );
			return false;
		}
		if (trim( $this->action ) == '') {
			$this->setError( JText::_('Please provide an action.') );
			return false;
		}
		return true;
	}

	public function getRecords()
	{
		$query  = "SELECT x.*, c.name 
					FROM $this->_tbl AS x, #__components AS c
					WHERE x.component=c.option AND c.parent=0
					ORDER BY x.component, x.action DESC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	public function getComponents()
	{
		$query  = "SELECT DISTINCT x.component 
					FROM $this->_tbl AS x
					ORDER BY x.component ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}

