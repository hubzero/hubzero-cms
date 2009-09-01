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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.event.plugin');

class plgXMessageHandler extends JPlugin
{
	public function plgXMessageHandler(& $subject) 
	{
		parent::__construct($subject, NULL);
	}

	//-----------

	public function onTakeAction( $type, $uids=array(), $component='', $element=null ) 
	{
		ximport('xmessage');
		
		// Do we have the proper bits?
		if (!$element || !$component || !$type) {
			return false;
		}
		
		// Do we have any user IDs?
		if (count($uids) > 0) {
			$database =& JFactory::getDBO();
			
			// Loop through each ID
			foreach ($uids as $uid) 
			{
				// Find any actions the user needs to take for this $component and $element
				$action = new XMessageAction( $database );
				$mids = $action->getActionItems( $type, $component, $element, $uid );

				// Check if the user has any action items
				if (count($mids) > 0) {
					/*$recipient = new XMessageRecipient( $database );
					if (!$recipient->setState( 1, $mids )) {
						$this->setError( JText::sprintf('Unable to update recipient records %s for user %s', implode(',',$mids), $uid) );
					}*/
					foreach ($mids as $mid) 
					{
						$xseen = new XMessageSeen( $database );
						$xseen->mid = $mid;
						$xseen->uid = $uid;
						$xseen->loadRecord();
						if ($xseen->whenseen == '' || $xseen->whenseen == '0000-00-00 00:00:00' || $xseen->whenseen == NULL) {
							$xseen->whenseen = date( 'Y-m-d H:i:s', time() );
							$xseen->store( true );
						}
					}
				}
			}
		}
		
		return true;
	}
	
	//-----------
	
	public function onSendMessage( $type, $subject, $message, $from=array(), $to=array(), $component='', $element=null, $description='' ) 
	{
		ximport('xmessage');
		
		// Do we have a message?
		if (!$message) {
			return false;
		}
		
		// Do we have a subject line? If not, create it from the message
		if (!$subject && $message) {
			$subject = substr($message, 0, 70);
			if (strlen($subject) >= 70) {
				$subject .= '...';
			}
		}
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Create the message object and store it in the database
		$xmessage = new XMessage( $database );
		$xmessage->subject    = $subject;
		$xmessage->message    = $message;
		$xmessage->created    = date( 'Y-m-d H:i:s', time() );
		$xmessage->created_by = $juser->get('id');
		$xmessage->component  = $component;
		$xmessage->type       = $type;
		if (!$xmessage->store()) {
			return $xmessage->getError();
		}
		
		// Does this message require an action?
		$action = new XMessageAction( $database );
		if ($element || $description) {
			$action->class   = $component;
			$action->element = $element;
			$action->description = $description;
			if (!$action->store()) {
				return $action->getError();
			}
		}
		
		// Do we have any recipients?
		if (count($to) > 0) {
			// Load plugins
			//JPluginHelper::importPlugin( 'xmessage' );
			$dispatcher =& JDispatcher::getInstance();
			
			// Loop through each recipient
			foreach ($to as $uid) 
			{
				// Create a recipient object that ties a user to a message
				$recipient = new XMessageRecipient( $database );
				$recipient->uid = $uid;
				$recipient->mid = $xmessage->id;
				$recipient->created = date( 'Y-m-d H:i:s', time() );
				$recipient->expires = date( 'Y-m-d H:i:s', time() + (168 * 24 * 60 * 60) );
				$recipient->actionid = (is_object($action)) ? $action->id : 0;
				
				
				// Get the user's methods for being notified
				$notify = new XMessageNotify( $database );
				$methods = $notify->getRecords( $uid, $type );

				$user =& JUser::getInstance($uid);
				
				// Do we have any methods?
				if ($methods) {
					// Loop through each method
					foreach ($methods as $method) 
					{
						$action = strtolower($method->method);

						if ($action == 'internal') {
							if (!$recipient->store()) {
								$this->setError( $recipient->getError() );
							}
						} else {
							if (!$dispatcher->trigger( 'onMessage', array($from, $xmessage, $user, $action) )) {
								$this->setError( JText::sprintf('Unable to message user %s with method %s', $uid, $action) );
							}
						}
					}
				} else {
					// First check if they have ANY methods saved (meaning they've changed their default settings)
					// If They do have some methods, then they simply turned off everything for this $type
					$methods = $notify->getRecords( $uid );
					if (!$methods || count($methods) <= 0) {
						// Load the default method
						$p = JPluginHelper::getPlugin('members','messages');
						$pp = new JParameter( $p->params );

						$d = $pp->get('default_method');
						$d = ($d) ? $d : 'email';

						if (!$recipient->store()) {
							$this->setError( $recipient->getError() );
						}

						// Use the Default in the case the user has no methods
						if (!$dispatcher->trigger( 'onMessage', array($from, $xmessage, $user, $d) )) {
							$this->setError( JText::sprintf('Unable to message user %s with method %s', $uid, $action) );
						}
					}
				}
			}
		}
		
		return true;
	}
}
