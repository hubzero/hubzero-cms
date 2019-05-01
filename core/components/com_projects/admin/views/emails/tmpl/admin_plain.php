<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$projectUrl = $base . '/projects/' . $this->project->get('alias');

$message  = $this->subject . "\n";
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
$message .= Lang::txt('COM_PROJECTS_EMAIL_URL') . ': ' . $projectUrl . "\n";
$message .= '-------------------------------' . "\n";

// Append a message
if ($this->message)
{
	$message .= Lang::txt('COM_PROJECTS_MSG_MESSAGE_FROM_ADMIN') . ': ' . "\n";
	$message .= $this->message . "\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
