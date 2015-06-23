<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// get base url
$groupLink  = rtrim(Request::base(), '/') . '/' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn'));
$acceptLink = rtrim(Request::base(), '/') . '/' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=accept');

// tell who just invited them on which hub
$message  = Lang::txt('COM_GROUPS_INVITE_EMAIL_INVITED_BY', $this->user->get('name'), $this->sitename)."\n\n";

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
$message .= Lang::txt('To ACCEPT this invitation, please click here:') . "\n";
$message .= $acceptLink . "\n\n";

// learn more
$message .= Lang::txt('To learn more or access the group after joining, please go to:') . "\n";
$message .= $groupLink . "\n\n";

// if questions email the invitor
$message .= Lang::txt('COM_GROUPS_INVITE_EMAIL_QUESTIONS', $this->user->get('name'), $this->user->get('email'))."\n";

echo $message;
