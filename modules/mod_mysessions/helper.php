<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
		
		$xprofile =& Hubzero_Factory::getProfile();
		if (is_object($xprofile)) {
			if (in_array('middleware', $xprofile->get('admin'))) {
				$this->authorized = 'admin';
			}
		}
		
		$jacl =& JFactory::getACL();
		$jacl->addACL( 'com_tools', 'manage', 'users', 'super administrator' );
		$jacl->addACL( 'com_tools', 'manage', 'users', 'administrator' );
		$jacl->addACL( 'com_tools', 'manage', 'users', 'manager' );
		
		$juser =& JFactory::getUser();
		
		// Get a connection to the middleware database
		$mwdb =& MwUtils::getMWDBO();

		$mconfig = JComponentHelper::getParams( 'com_tools' );

		// Ensure we have a connection to the middleware
		$this->error = false;
		if (!$mwdb 
		 || !$mconfig->get('mw_on') 
		 || ($mconfig->get('mw_on') > 1 && !$juser->authorize('com_tools', 'manage'))) {
			$this->error = true;
			return false;
		}
		
		$ms = new MwSession( $mwdb );
		$this->sessions = $ms->getRecords( $juser->get('username'), '', false );
		if ($this->authorized) {
			// Add the JavaScript that does the AJAX magic to the template
			$document =& JFactory::getDocument();
			$document->addScript('/modules/mod_mysessions/mod_mysessions.js');
				
			$this->allsessions = $ms->getRecords( $juser->get('username'), '', $this->authorized );
		}
		
		$rconfig = JComponentHelper::getParams( 'com_resources' );
		$this->supportedtag = $rconfig->get('supportedtag');
		
		$database =& JFactory::getDBO();
		if ($this->supportedtag) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
			$this->rt = new ResourcesTags( $database );
		}
	
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_mysessions');
	}
}

