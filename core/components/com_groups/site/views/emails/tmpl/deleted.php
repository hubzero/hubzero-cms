<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = ltrim(Route::url('index.php?option='.$this->option.'&cname='. $this->group->get('cn')), '/');

$message  = Lang::txt('COM_GROUPS_DELETE_BY_MEMBER', $this->gcn, $this->user->get('username'))."\n\n";
if ($this->msg)
{
	$message .= stripslashes($this->msg)."\n\n";
}
$message .= Lang::txt('COM_GROUPS_DELETE_QUESTIONS', $this->user->get('username'), $this->user->get('email'))."\n";
$message .= rtrim(Request::base(), '/') . '/' . $sef."\n";

echo $message;
