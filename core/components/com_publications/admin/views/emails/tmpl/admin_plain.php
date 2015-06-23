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

// No direct access
defined('_HZEXEC_') or die();

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$link = $base . DS . trim(Route::url($this->model->link('editversion')), DS);
$pubLink = $base . DS . trim(Route::url($this->model->link('version')), DS);

$message  = $this->subject . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' "' . $this->model->title . '" (id #' . $this->model->id . ')' . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_EMAIL_URL') . ': ' . $pubLink . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias') ;

if ($this->project->isProvisioned())
{
	$message .= ' - ' . Lang::txt('COM_PROJECTS_PROVISIONED');
}

$message .= ')' . "\n";

$message .= Lang::txt('COM_PUBLICATIONS_EMAIL_PROJECT_URL') . ': ' . $link . "\n";
$message .= '-------------------------------' . "\n";

// Append a message
if ($this->message)
{
	$message .= Lang::txt('COM_PUBLICATION_MSG_MESSAGE_FROM_ADMIN') . ': ' . "\n";
	$message .= $this->message . "\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;