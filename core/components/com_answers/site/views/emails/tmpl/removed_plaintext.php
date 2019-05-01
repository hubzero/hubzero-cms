<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!isset($this->link))
{
	$this->link = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->question->link()), '/');
}

$message  = Lang::txt('COM_ANSWERS_EMAIL_AUTO_GENERATED') . "\n";
$message .= '----------------------------' . "\n";
$message  = Lang::txt('COM_ANSWERS_EMAIL_Q_REMOVED');
$message .= Lang::txt('COM_ANSWERS_EMAIL_Q_REMOVED_NO_POINTS') . "\n";
$message .= '----------------------------' . "\n\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_QUESTION')) . ': ' . $this->question->get('id') . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_SUMMARY')) . ': ' . $this->question->get('subject') . "\n";
$message .= '----------------------------' . "\n\n";
$message .= $this->link . "\n";

echo $message;
