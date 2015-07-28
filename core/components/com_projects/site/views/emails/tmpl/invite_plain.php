<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');

$sef  = 'projects/' . $this->project->get('alias');
$link = rtrim($base, '/') . '/' . trim($sef, '/');

if ($this->uid == $this->project->get('created_by_user'))
{
	$message  = Lang::txt('COM_PROJECTS_EMAIL_CREATOR_NEW_PROJECT');
	$message .= "\n";
	$message .= '-------------------------------' . "\n";
}
else {
	$message  = $this->project->owner('name') . ' ';
	$message .= $this->uid ? Lang::txt('COM_PROJECTS_EMAIL_ADDED_YOU') : Lang::txt('COM_PROJECTS_EMAIL_INVITED_YOU');
	$message .= ' "' . $this->project->get('title') . '" ' . Lang::txt('COM_PROJECTS_EMAIL_IN_THE_ROLE') . ' ';
	$message .= $this->role == 1 ? Lang::txt('COM_PROJECTS_LABEL_OWNER') : Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
	$message .= "\n";
	$message .= '-------------------------------' . "\n";
}

$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title')
		 . ' (' . $this->project->get('alias') . ')' . "\n";
$message .= ucfirst(Lang::txt('COM_PROJECTS_CREATED')) . ' '
		 . Date::of($this->project->get('created'))->format('M d, Y') . ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
$message .= $this->project->groupOwner()
			 ? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
			 : $this->project->owner('name');
$message .= "\n";
$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n\n";

$sef .= $this->uid ? '' : '/?confirm=' . $this->code . '&email=' . $this->email;
$link = rtrim($base, '/') . '/' . trim($sef, '/');

if ($this->uid)
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_ACCESS_PROJECT') . "\n";
}
else
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT') . ' ' . Config::get('sitename') . ' ';
	$message .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT') . "\n";
}
$message .= $link . "\n\n";

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;

?>
