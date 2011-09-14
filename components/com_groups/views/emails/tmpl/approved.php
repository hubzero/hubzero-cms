<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri =& JURI::getInstance();

$sef = JRoute::_('index.php?option='.$this->option.'&gid='. $this->group->get('cn'));
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}

$message  = JText::sprintf('GROUPS_EMAIL_MSG_APPROVED', $this->hubShortName)."\n\n";
$message .= JText::_('GROUPS_ID').': '. $this->group->get('cn') ."\n";
$message .= JText::_('GROUPS_TITLE').': '. $this->group->get('description') ."\n";
switch ($this->group->get('access'))
{
	case 4: $privacy = JText::_('GROUPS_ACCESS_PRIVATE');   break;
	case 3: $privacy = JText::_('GROUPS_ACCESS_PROTECTED'); break;
	case 0: $privacy = JText::_('GROUPS_ACCESS_PUBLIC');    break;
}
$message .= JText::_('GROUPS_PRIVACY').': '. $privacy ."\n";
$message .= "\n";
$message .= JText::_('GROUPS_USE_LINK_TO_REVIEW_GROUP')."\n";
$message .= $juri->base().$sef."\n";

echo $message;
?>