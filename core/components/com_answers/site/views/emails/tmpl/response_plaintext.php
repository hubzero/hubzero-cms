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
$message .= strtoupper(Lang::txt('COM_ANSWERS_CREATED')) . ': ' . $this->question->get('created') ."\n";
$message .= '----------------------------' . "\n\n";
$message .= 'A response has been posted to Question #' . $this->question->get('id') . ' by: ';
$message .= ($this->row->get('anonymous')) ? Lang::txt('JANONYMOUS') . "\n" : $this->row->creator->get('name') . "\n";
$message .= 'Response created: ' . $this->row->get('created') . "\n";
$message .= 'Response: ' . "\n\n";
$message .= '"' . $this->row->content . '"' . "\n\n";
$message .= 'To view the full question and responses, go to: ' . "\n";
$message .= $this->link . "\n";

echo $message;
