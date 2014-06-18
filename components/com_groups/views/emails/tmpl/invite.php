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

// get base url
$JURI       =& JURI::getInstance();
$groupLink  = rtrim($JURI->base(), DS) . JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn'));
$acceptLink = rtrim($JURI->base(), DS) . JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=accept');

// tell who just invited them on which hub
$message  = JText::sprintf('COM_GROUPS_INVITE_EMAIL_INVITED_BY', $this->juser->get('name'), $this->sitename)."\n\n";

// what group
$message .= $this->group->get('description')."\n\n";

// extra message
if ($this->msg)
{
	$message .= '====================='."\n";
	$message .= stripslashes($this->msg)."\n";
	$message .= '====================='."\n\n";
}

// accept link
$message .= JText::_('To ACCEPT this invitation, please click here:') . "\n";
$message .= $acceptLink . "\n\n";

// learn more
$message .= JText::_('To learn more or access the group after joining, please go to:') . "\n";
$message .= $groupLink . "\n\n";

// if questions email the invitor
$message .= JText::sprintf('COM_GROUPS_INVITE_EMAIL_QUESTIONS', $this->juser->get('name'), $this->juser->get('email'))."\n";

echo $message;
?>