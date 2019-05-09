<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$link = $base . '/projects/' . $this->model->project()->get('alias') . '/publications/' . $this->model->get('id');
$pubLink = $base . '/publications/' . $this->model->get('id') . '/' . $this->model->get('version_number');

$message  = $this->subject . "\n";
$message .= '-------------------------------' . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' "' . $this->model->title . '" (id #' . $this->model->id . ')' . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_EMAIL_URL') . ': ' . $pubLink . "\n";
$message .= Lang::txt('COM_PUBLICATIONS_PROJECT') . ': ' . $this->project->get('title') . ' (' . $this->project->get('alias');

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
