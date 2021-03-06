<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = ltrim(Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&active=members'), '/');

$message  = Lang::txt('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED_DETAILS', $this->sitename)."\n\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_GROUP').': '. $this->group->get('description') .' ('.$this->group->get('cn').')'."\n";
$message .= "\t".' '.Lang::txt('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED').':'."\n";
$message .= "\t\t". $this->user->get('name')."\n";
$message .= "\t\t". $this->user->get('username') .' ('. $this->user->get('email') . ')'."\n\n";
$message .= Lang::txt('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED_LINK')."\n";
$message .= rtrim(Request::base(), '/') . '/' . $sef."\n";

echo $message;
