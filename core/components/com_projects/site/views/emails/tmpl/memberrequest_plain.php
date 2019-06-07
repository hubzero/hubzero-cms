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

$sef  = Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias'));

$link = rtrim($base, DS) . DS . trim($sef, DS);

$message  = Lang::txt('COM_PROJECTS_EMAIL_ADMIN_NEW_PUB_STATUS') . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PROJECTS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias');

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
