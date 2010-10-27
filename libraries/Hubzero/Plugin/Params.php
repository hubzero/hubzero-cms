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
