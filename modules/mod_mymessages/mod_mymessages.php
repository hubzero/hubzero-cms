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
// "My Messages"
//    This module displays unread messages for the 
//    user currently logged in.
//-------------------------------------------------------------

class modMyMessages
{
	private $attributes = array();

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
		$moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;

		// Check for the existence of required tables that should be
		// installed with the com_support component
		$database->setQuery("SHOW TABLES");
		$tables = $database->loadResultArray();
		
		if ($tables && array_search($database->_table_prefix.'xmessage', $tables)===false) {
			// Support tickets table not found!
			echo 'Required database table not found.';
			return false;
	    }

		// Find the user's most recent support tickets
		ximport('xmessage');
		$recipient = new XMessageRecipient( $database );
		$rows = $recipient->getUnreadMessages( $juser->get('id'), $limit );
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_mymessages');
		
		// Build the HTML
		$html  = '<div';
		$html .= ($moduleclass) ? ' class="'.$moduleclass.'">'."\n" : '>'."\n";
		//$html .= '<h4>New Messages</h4>'."\n";
		//$html .= $this->_messagelist( $rows );
		if (count($rows) <= 0) {
			$html .= "\t".'<p>'.JText::_('NO_MESSAGES').'</p>'."\n";
		} else {
			$html .= "\t".'<ul class="expandedlist">'."\n";
			foreach ($rows as $row)
			{
				if ($row->actionid) {
					$cls = 'actionitem';
				} else {
					$cls = 'box';
				}
				$html .= "\t\t".'<li class="'.$cls.'">'."\n";
				$html .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages'.a.'msg='.$row->id).'">'.stripslashes($row->subject).'</a>'."\n";
				$html .= "\t\t\t".'<span><span>'.JHTML::_('date', $row->created, '%d %b, %Y %I:%M %p').'</span></span>'."\n";
				$html .= "\t\t".'</li>';
			}
			$html .= "\t".'</ul>'."\n";
		}
		$html .= "\t".'<ul class="module-nav">'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages').'">'.JText::_('ALL_MESSAGES').'</a></li>'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=messages&task=settings').'">'.JText::_('MESSAGE_SETTINGS').'</a></li>'."\n";
		$html .= "\t".'</ul>'."\n";
		$html .= '</div>'."\n";
		
		// Output the HTML
		echo $html;
	}
}

//-------------------------------------------------------------

$modmymessages = new modMyMessages();
$modmymessages->params = $params;

require( JModuleHelper::getLayoutPath('mod_mymessages') );
?>
