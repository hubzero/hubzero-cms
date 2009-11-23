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

//-------------------------------------------------------------
// Joomla module
// "My Points"
//    This module displays a point total and latest transations 
//-------------------------------------------------------------

class modMyPoints
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$this->moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;
		$this->limit = $limit;
		$this->error = false;
		
		// Check for the existence of required tables that should be
		// installed with the com_support component
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();
		
		if ($tables && array_search($database->_table_prefix.'users_points', $tables)===false) {
			// MEssages table not found!
			$this->error = true;
		} else {
			// Find the user's most recent support tickets
			ximport('bankaccount');

			$BTL  = new BankTeller( $database, $juser->get('id') );
			$this->summary = $BTL->summary();
			$this->history = $BTL->history($limit);

			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_mypoints');
		}
	}
}

//-------------------------------------------------------------

$modmypoints = new modMyPoints( $params );
$modmypoints->display();

require( JModuleHelper::getLayoutPath('mod_mypoints') );
?>