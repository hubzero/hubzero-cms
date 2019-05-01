<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = ltrim(Route::url('index.php?option='.$this->option.'&gid='. $this->course->get('cn')), '/');

$message  = Lang::txt('COURSES_USER_HAS_DELETED_COURSE', $this->gcn, $this->user->get('username'))."\n\n";
if ($this->msg)
{
	$message .= stripslashes($this->msg)."\n\n";
}
$message .= Lang::txt('COURSES_EMAIL_USER_IF_QUESTIONS', $this->user->get('username'), $this->user->get('email'))."\n";
$message .= rtrim(Request::base(), '/') . '/' . $sef."\n";

echo $message;
