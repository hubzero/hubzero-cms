<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&active=members');

$message  = Lang::txt('COM_GROUPS_JOIN_REQUEST_EMAIL_DETAILS', $this->sitename)."\n\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_GROUP').': '. $this->group->get('description') .' ('.$this->group->get('cn').')'."\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_JOIN_REQUEST').': '."\n";
$message .= "\n".'---------------------------------------------------------------------------------------'."\n";
$message .= "\t".$this->user->get('name')."\n";
$message .= "\t\t". $this->user->get('username') .' ('. $this->user->get('email') . ')';
if ($this->group->get('join_policy') == 1)
{
	$message .= "\r\n" . Lang::txt('COM_GROUPS_JOIN_REQUEST_APPROVE_BECAUSE').' '."\r\n". stripslashes($this->row->reason);
}
$message .= "\n".'---------------------------------------------------------------------------------------'."\n\n";
$message .= Lang::txt('COM_GROUPS_JOIN_REQUEST_LINK')."\n";
$message .= rtrim(Request::base(), '/').'/'.ltrim($sef, '/')."\n";

echo $message;
