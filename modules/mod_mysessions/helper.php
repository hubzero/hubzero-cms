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

class modMySessions
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

	private function _setTimeout( $sess ) 
	{
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		$ms->load( $sess );
		$ms->timeout = 1209600;
		$ms->store();
	}
	
	//-----------

	private function _getTimeout( $sess )
	{
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		$remaining = $ms->getTimeout();
		
		$tl = 'unknown';

		if (is_numeric($remaining)) {
			$days_left = floor($remaining/60/60/24);
			$hours_left = floor(($remaining - $days_left*60*60*24)/60/60);
			$minutes_left = floor(($remaining - $days_left*60*60*24 - $hours_left*60*60)/60);
			$left = array($days_left, $hours_left, $minutes_left);
			
			$tl  = '';
			$tl .= ($days_left > 0) ? $days_left .' days, ' : '';
			$tl .= ($hours_left > 0) ? $hours_left .' hours, ' : '';
			$tl .= ($minutes_left > 0) ? $minutes_left .' minute' : '';
			$tl .= ($minutes_left > 1) ? 's' : '';
		}
		return $tl;
	}

	//-----------
	
	public function display()
	{
		// Get the module parameters
		$params =& $this->params;
		$this->moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$this->show_storage = $params->get( 'show_storage' );
		
		// Check if the user is an admin.
		$this->authorized = false;
		
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			if (in_array('middleware', $xuser->get('admin'))) {
				$this->authorized = 'admin';
			}
		}
		
		$juser =& JFactory::getUser();
		
		// Get a connection to the middleware database
		$mwdb =& MwUtils::getMWDBO();
	
		// Ensure we have a connection to the middleware
		$this->error = false;
		if (!$mwdb) {
			$this->error = true;
			return false;
		}
		
		$ms = new MwSession( $mwdb );
		$this->sessions = $ms->getRecords( $juser->get('username'), '', false );
		if ($this->authorized) {
			$this->allsessions = $ms->getRecords( $juser->get('username'), '', $this->authorized );
		}
	
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_mysessions');
	}
}
