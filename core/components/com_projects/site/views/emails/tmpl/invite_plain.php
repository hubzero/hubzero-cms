<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
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
	$message  = User::get('name') . ' '; //$this->project->owner('name') . ' ';
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
