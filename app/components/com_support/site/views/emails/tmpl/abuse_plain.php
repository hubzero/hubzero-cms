<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/new?case=' . $this->report->id;
}
else
{
	$sef = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new&case=' . $this->report->id);
}
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$base = rtrim(str_replace('/administrator', '', $base), '/');

$message  = '----------------------------' . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_CASE_NUM')) . ': ' . $this->report->id . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REASON')) . ': ' . $this->report->subject . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REPORTED')) . ': ' . $this->report->created . "\n";
if (!$this->author)
{
	$reporter = User::getInstance($this->report->created_by);

	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REPORTED_BY')) . ': ' . $this->escape($reporter->get('name')) . '(' . $this->escape($reporter->get('username')) . ')' . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_COMMENTS')) . ': "' . $this->escape($this->report->report) . '"' . "\n";
}
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REVIEWED')) . ': ' . $this->report->reviewed . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_STATUS')) . ': ' . Lang::txt('COM_SUPPORT_EMAIL_STATUS_REMOVED') . "\n";
if ($this->report->note && !$this->author)
{
	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_NOTE')) . ': ' . $this->report->note . "\n";
}
if ($this->author)
{
	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_DISPUTE')) . ': ' . strip_tags(Lang::txt('COM_SUPPORT_EMAIL_DISPUTE_EXPLANATION', '#', $this->report->id)) . "\n";
}
$message .= '----------------------------'."\n\n";

if ($this->reported)
{
	$this->commentor = User::getInstance($this->reported->author);

	$message .= Lang::txt('COM_SUPPORT_EMAIL_CREATED_BY') . ': ' . stripslashes($this->commentor->get('name')) . ' (' . $this->commentor->get('username') . ')' . "\n";
	$message .= Lang::txt('COM_SUPPORT_EMAIL_CREATED') . ': ' . $this->reported->created . "\n\n";

	$message .= str_replace('<br />', '', $this->reported->text);
}
else
{
	$message .= Lang::txt('COM_SUPPORT_EMAIL_REPORTED_ITEM_NOT_FOUND') . "\n\n";
}
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n";
