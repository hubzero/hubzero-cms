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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class Hubzero_Plugin_Params extends JTable 
{
	var $id        = NULL;  // @var int(11) Primary key
	var $object_id = NULL;  // @var int(11)
	var $folder    = NULL;  // @var varchar(100)
	var $element   = NULL;  // @var varchar(100)
	var $params    = NULL;  // @var text
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__plugin_params', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->object_id ) == '') {
			$this->setError( JText::_('Entry must have an object ID') );
			return false;
		}
		if (trim( $this->folder ) == '') {
			$this->setError( JText::_('Entry must have a folder') );
			return false;
		}
		if (trim( $this->element ) == '') {
			$this->setError( JText::_('Entry must have an element') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadPlugin( $oid=null, $folder=null, $element=NULL ) 
	{
		if (!$oid) {
			$oid = $this->object_id;
		}
		if (!$folder) {
			$folder = $this->folder;
		}
		if (!$element) {
			$element = $this->element;
		}
		if (!$oid || !$element || !$folder) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE object_id='$oid' AND folder='$folder' AND element='$element' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getCustomParams( $oid=null, $folder=null, $element=null ) 
	{
		if (!$oid) {
			$oid = $this->object_id;
		}
		if (!$folder) {
			$folder = $this->folder;
		}
		if (!$element) {
			$element = $this->element;
		}
		if (!$oid || !$folder || !$element) {
			return null;
		}
		
		$this->_db->setQuery( "SELECT params FROM $this->_tbl WHERE object_id=$oid AND folder='$folder' AND element='$element' LIMIT 1" );
		$result = $this->_db->loadResult();
		
		$params = new JParameter( $result );
		return $params;
	}
	
	//-----------
	
	public function getDefaultParams( $folder=null, $element=null ) 
	{
		if (!$folder) {
			$folder = $this->folder;
		}
		if (!$element) {
			$element = $this->element;
		}
		if (!$folder || !$element) {
			return null;
		}
		
		$plugin = JPluginHelper::getPlugin( $folder, $element );
		$params = new JParameter( $plugin->params );
		return $params;
	}
	
	//-----------
	
	public function getParams( $oid=null, $folder=null, $element=null ) 
	{
		$rparams = $this->getCustomParams( $oid, $folder, $element );
		$params = $this->getDefaultParams( $folder, $element );
		$params->merge( $rparams );
		return $params;
	}
}

