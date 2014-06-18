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

$sef = JRoute::_('index.php?option='.$this->option.'&gid='. $this->g_cn.'&active=members');
if (substr($sef,0,1) == '/') {
	$sef = substr($sef,1,strlen($sef));
}

$message = $this->juser->get('name');
$message .= ','."\n";
$message .= '"'.$this->juser->get('username') .'" ('. $this->juser->get('email') .')'."\n";
if ($this->isNew) {
	$message .= JText::sprintf('COURSES_USER_HAS_REQUESTED_COURSE', $this->sitename) .':'."\n\n";
	$message .= JText::_('COURSES_ID').': '. $this->g_cn ."\n";
} else {
	$message .= JText::sprintf('COURSES_USER_HAS_CHANGED_COURSE', $this->sitename) .':'."\n\n";
	$message .= JText::_('COURSES_TITLE').': '. $this->course->get('description') ."\n";
	switch ($this->course->get('join_policy'))
	{
		case 3: $policy = JText::_('COURSES_POLICY_CLOSED');   break;
		case 2: $policy = JText::_('COURSES_POLICY_INVITE');   break;
		case 1: $policy = JText::_('COURSES_POLICY_RESTRICTED'); break;
		case 0: $policy = JText::_('COURSES_POLICY_OPEN');    break;
	}
	$message .= JText::_('COURSES_JOIN_POLICY').': '. $policy ."\n";
	switch ($this->course->get('privacy'))
	{
		case 4: $privacy = JText::_('COURSES_ACCESS_PRIVATE');   break;
		case 3: $privacy = JText::_('COURSES_ACCESS_PROTECTED'); break;
		case 0: $privacy = JText::_('COURSES_ACCESS_PUBLIC');    break;
	}
	$message .= JText::_('COURSES_PRIVACY').': '. $privacy ."\n";

	/*
	switch ($this->course->get('access'))
	{
		case 4: $access = JText::_('COURSES_ACCESS_PRIVATE');   break;
		case 3: $access = JText::_('COURSES_ACCESS_PROTECTED'); break;
		case 0: $access = JText::_('COURSES_ACCESS_PUBLIC');    break;
	}
	$message .= JText::_('COURSES_CONTENT_PRIVACY').': '. $access ."\n";
	*/

	$message .= "\n";
	$message .= JText::_('COURSES_NOW_DEFINED_AS').':'."\n";
}
$message .= JText::_('COURSES_TITLE').': '. $this->g_description ."\n";
switch ($this->g_join_policy)
{
	case 3: $policy = JText::_('COURSES_POLICY_CLOSED');   break;
	case 2: $policy = JText::_('COURSES_POLICY_INVITE');   break;
	case 1: $policy = JText::_('COURSES_POLICY_RESTRICTED'); break;
	case 0: $policy = JText::_('COURSES_POLICY_OPEN');    break;
}
$message .= JText::_('COURSES_JOIN_POLICY').': '. $policy ."\n";
switch ($this->g_privacy)
{
	case 4: $privacy = JText::_('COURSES_ACCESS_PRIVATE');   break;
	case 3: $privacy = JText::_('COURSES_ACCESS_PROTECTED'); break;
	case 0: $privacy = JText::_('COURSES_ACCESS_PUBLIC');    break;
}
$message .= JText::_('COURSES_PRIVACY').': '. $privacy ."\n";

/*
switch ($this->g_access)
{
	case 4: $access = JText::_('COURSES_ACCESS_PRIVATE');   break;
	case 3: $access = JText::_('COURSES_ACCESS_PROTECTED'); break;
	case 0: $access = JText::_('COURSES_ACCESS_PUBLIC');    break;
}
$message .= JText::_('COURSES_CONTENT_PRIVACY').': '. $access ."\n";
*/

$message .= "\n";
$message .= JText::_('COURSES_USE_LINK_TO_REVIEW_COURSE')."\n";
$message .= $juri->base().$sef."\n";

echo $message;
?>