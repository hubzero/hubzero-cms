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

class modXPoll
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
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xpoll'.DS.'xpoll.class.php' );
		
		$database =& JFactory::getDBO();
		
		$params =& $this->params;
		$this->formid = $params->get( 'formid' );

		// Load the latest poll
		$poll = new XPollPoll( $database );
		$poll->getLatestPoll();

		// Did we get a result from the database?
		if ($poll->id && $poll->title) {
			$this->poll = $poll;
			
			$xpdata = new XPollData( $database );
			$this->options = $xpdata->getPollOptions( $poll->id, false );
			
			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_xpoll');
		}
	}
}

//-------------------------------------------------------------

$modxpoll = new modXPoll( $params );
$modxpoll->display();

require( JModuleHelper::getLayoutPath('mod_xpoll') );
?>