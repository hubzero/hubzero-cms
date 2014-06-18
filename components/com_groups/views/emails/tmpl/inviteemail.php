<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri = JURI::getInstance();

$sef = JRoute::_('index.php?option='.$this->option.'&cn='. $this->group->get('cn'));
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}

$message  = JText::sprintf('COM_GROUPS_INVITE_EMAIL_INVITED_BY', $this->juser->get('name'), $this->sitename)."\n\n";
$message .= JText::_('COM_GROUPS_GROUP').': '.$this->group->get('description')."\n\n";
//$message .= $juri->base().$sef."\n\n";
if ($this->msg) {
	$message .= '====================='."\n";
	$message .= stripslashes($this->msg)."\n";
	$message .= '====================='."\n\n";
}

$message .= "If you already have a registered account on ".$this->sitename.", click or copy and paste the link below into a browser window. \r\n";
$sef = JRoute::_('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&task=accept&token='.$this->token);
//$sef = JRoute::_('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&task=accept');
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}
$message .= $juri->base().$sef."\n\n";
$message .= "--------------------------------------------\n\n";

//$message .= "If you do not have an account on ".$this->sitename.", you must first click or copy and paste the first link, where you will register for an account. Then you must come back to click or copy and paste link two. \n\n";

//$sef = JRoute::_('index.php?option=com_members&controller=register');
//if (substr($sef,0,1) == '/') {
//	$sef = substr($sef,1,strlen($sef));
//}
//$message .= "1. ".$juri->base().$sef."\n\n";

//$sef = JRoute::_('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&task=accept&token='.$this->token);
//if (substr($sef,0,1) == '/') {
//	$sef = substr($sef,1,strlen($sef));
//}
//$message .= "2. ".$juri->base().$sef."\n\n";

$message .= "If you DO NOT have an account on " . $this->sitename . ", please follow this link! \n\n";

$return = DS . "groups" . DS . $this->group->get('cn') . DS . "accept?token=" . $this->token;
//$return = DS . "groups" . DS . $this->group->get('cn') . DS . "accept";

$sef = JRoute::_('index.php?option=com_members&controller=register');
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}

if(substr($sef,-1,1) == DS)
{
	$sef = rtrim($sef, DS);
}

$message .= $juri->base().$sef."?return=".base64_encode($return)."\n\n\n";

$message .= "--------------------------------------------\n\n";
$message .= JText::sprintf('COM_GROUPS_INVITE_EMAIL_QUESTIONS', $this->juser->get('name'), $this->juser->get('email'))."\n";

echo $message;
?>