<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

$base = rtrim(Request::base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'projects/' . $this->model->get('alias');
}
else
{
	$sef = Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias'));
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

$message  = User::get('name') . ' ';
if ($this->model->isProvisioned())
{
	$message .= $this->uid
			? Lang::txt('COM_PROJECTS_EMAIL_ADDED_AS_PUB_AUTHOR')
			: Lang::txt('COM_PROJECTS_EMAIL_INVITED_AS_PUB_AUTHOR');
	$message .= ' "' . $this->model->get('title') . '"';
	$message .= "\n";
	$message .= '-------------------------------' . "\n";
}
else
{
	$message .= $this->uid ? Lang::txt('COM_PROJECTS_EMAIL_ADDED_YOU') : Lang::txt('COM_PROJECTS_EMAIL_INVITED_YOU');
	$message .= ' "' . $this->model->get('title') . '" ' . Lang::txt('COM_PROJECTS_EMAIL_IN_THE_ROLE') . ' ';
	$message .= $this->role == 1 ? Lang::txt('COM_PROJECTS_LABEL_OWNER') : Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
	$message .= "\n";
	$message .= '-------------------------------' . "\n";
	$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->model->get('title') . ' (' . $this->model->get('alias') . ')' . "\n";
	$message .= ucfirst(Lang::txt('COM_PROJECTS_CREATED')) . ' '
			 .Date::of($this->model->get('created'))->toLocal('M d, Y') . ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
	$message .= $this->model->groupOwner()
			 ? $this->model->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
			 : $this->model->owner('name');
	$message .= "\n";
	$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n\n";
}

$sef 	.= $this->uid ? '' : '/?confirm=' . $this->code . '&email=' . $this->email;
$link = rtrim($base, DS) . DS . trim($sef, DS);

if (User::get('id'))
{
	$message .= $this->model->isProvisioned()
			? Lang::txt('COM_PROJECTS_EMAIL_ACCESS_PUB_PROJECT') . "\n"
			: Lang::txt('COM_PROJECTS_EMAIL_ACCESS_PROJECT') . "\n";
}
else
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT_NEED_ACCOUNT') . ' ' . $this->hubShortName . ' ';
	$message .= Lang::txt('COM_PROJECTS_EMAIL_ACCEPT') . "\n";
}
$message .= $link . "\n\n";

$message .= Lang::txt('COM_PROJECTS_EMAIL_USER_IF_QUESTIONS') . ' ' . User::get('name') . '  - ' . User::get('email') . "\n";

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
