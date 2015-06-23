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

$sef = Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&active=members');

$message  = Lang::txt('COM_GROUPS_JOIN_REQUEST_EMAIL_DETAILS',$this->sitename)."\n\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_GROUP').': '. $this->group->get('description') .' ('.$this->group->get('cn').')'."\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_JOIN_REQUEST').': '."\n";
$message .= "\n".'---------------------------------------------------------------------------------------'."\n";
$message .= "\t".$this->user->get('name')."\n";
$message .= "\t\t". $this->user->get('username') .' ('. $this->user->get('email') . ')';
if ($this->group->get('join_policy') == 1)
{
	$message .= "\r\n" . Lang::txt('COM_GROUPS_JOIN_REQUEST_APPROVE_BECAUSE').' '."\r\n". stripslashes($this->row->reason);
}
$message .= "\n".'---------------------------------------------------------------------------------------'."\n\n";
$message .= Lang::txt('COM_GROUPS_JOIN_REQUEST_LINK')."\n";
$message .= rtrim(Request::base(), '/').'/'.ltrim($sef, '/')."\n";

echo $message;
