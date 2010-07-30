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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_extendedform' );

//-----------

class plgGroupsExtendedform extends JPlugin
{
	public function plgGroupsExtendedform(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'extendedform' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onGroupDelete( $group ) 
	{
		// Start the log text
		$log = JText::_('Example delete log').': ';
		
		// Do whatever you need for group deletion processing
		
		// Return the log
		return $log;
	}

	//-----------
	
	public function onGroupNew( $group ) 
	{
		// Start the log text
		
		// Do whatever you need for post group creation processing
		$createShare = JRequest::getVar('shareddir', 0, 'post');
		
		if ($createShare) {
			$cn = trim($group->cn);
			$datadir = '/data/groups'; // @FIXME: should be a com_groups parameter
			exec("sudo /usr/lib/hubzero/bin/addgroupdatadir $cn $datadir", $results, $retval);
			if ($retval == 0)
				$log = JText::_('New data directory created').': ';
			else
				$log = JText::_('Failed to create new data directory').': ';
			
		}

		// Return the log
		return $log;
	}
}
