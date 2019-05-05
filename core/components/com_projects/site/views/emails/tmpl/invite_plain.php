<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
