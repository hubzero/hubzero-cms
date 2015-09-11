<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

$sef  = Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias'));

$link = rtrim($base, DS) . DS . trim($sef, DS);

$message  = Lang::txt('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS') . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias') ;

if ($this->project->isProvisioned())
{
	$message .= ' - ' . Lang::txt('COM_PROJECTS_PROVISIONED');
}

$message .= ')' . "\n";

if (!$this->project->isProvisioned())
{
	$message .= ucfirst(Lang::txt('COM_PROJECTS_CREATED')) . ' '
		 . Date::of($this->project->get('created'))->toLocal('M d, Y') . ' '
		 . Lang::txt('COM_PROJECTS_BY') . ' ';
	$message .= $this->project->groupOwner()
			 ? $this->project->groupOwner('cn') . ' ' . Lang::txt('COM_PROJECTS_GROUP')
			 : $this->project->owner('name');
}
$message .= "\n";

if ($this->project->isPublic())
{
	$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $link . "\n";
}
$message .= '-------------------------------' . "\n\n";

// Append a message
if ($this->message)
{
	$message .= $this->message . "\n";
	$message .= '-------------------------------' . "\n\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;

?>
