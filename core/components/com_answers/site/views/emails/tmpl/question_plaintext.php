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
$message .= strtoupper(Lang::txt('COM_ANSWERS_QUESTION')) . ' #' . $this->question->get('id') . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_SUMMARY')) . ': ' . $this->question->get('subject') . "\n";
$message .= strtoupper(Lang::txt('COM_ANSWERS_CREATED')) . ': ' . $this->question->get('created') . "\n";
$message .= '----------------------------' . "\n\n";
$message .= 'A new question #' . $this->question->get('id') . ' has been posted by: ';
$message .= ($this->question->get('anonymous')) ? Lang::txt('JANONYMOUS') . "\n" : $this->question->creator->get('name') . "\n\n";
$message .= 'To view the full question and take actions, go to: ' . "\n";
$message .= $this->link . "\n";

echo $message;
