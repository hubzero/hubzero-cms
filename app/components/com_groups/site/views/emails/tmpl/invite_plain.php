<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// get base url
$groupLink  = rtrim(Request::base(), '/') . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')), '/');
$acceptLink = rtrim(Request::base(), '/') . '/' . ltrim(Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=accept'), '/');

// tell who just invited them on which hub
$message  = Lang::txt('COM_GROUPS_INVITE_EMAIL_INVITED_BY', $this->user->get('name'), $this->sitename)."\n\n";

// what group
$message .= $this->group->get('description')."\n\n";

// extra message
if ($this->msg)
{
	$message .= '====================='."\n";
	$message .= stripslashes($this->msg)."\n";
	$message .= '====================='."\n\n";
}

// accept link
$message .= Lang::txt('To ACCEPT this invitation, please click here:') . "\n";
$message .= $acceptLink . "\n\n";

// learn more
$message .= Lang::txt('To learn more or access the group after joining, please go to:') . "\n";
$message .= $groupLink . "\n\n";

// if questions email the invitor
$message .= Lang::txt('COM_GROUPS_INVITE_EMAIL_QUESTIONS', $this->user->get('name'), $this->user->get('email'))."\n";

echo $message;
