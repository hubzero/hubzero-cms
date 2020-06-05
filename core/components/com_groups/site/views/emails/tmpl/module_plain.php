<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base      = rtrim(str_replace('administrator', '', Request::base()), '/');
$groupLink = $base . '/groups/' . $this->group->get('cn');
$adminLink = $base . '/administrator/index.php?option=com_groups&gid=' . $this->group->get('cn') . '&controller=modules';

$message  = Lang::txt('Group Modules') . "\n\n";
$message .= "--------------------------------------------\n";
$message .= Lang::txt('Group:') . ' ' . strip_tags($this->group->get('description')) . "\n";
$message .= Lang::txt('Alias:') . ' ' . $this->group->get('cn') . "\n";
$message .= Lang::txt('Created:') . ' @ ' . Date::of($this->object->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of($this->object->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";

$profile = User::getInstance($this->object->get('created_by'));
$message .= Lang::txt('Created By:') . ' ' . $profile->get('name') . ' ('.$profile->get('username').')' . "\n";
$message .= Lang::txt('Status:') . ' ' . ($this->object->get('approved') == 1) ? Lang::txt('Approved') : Lang::txt('Unapproved') . "\n";
$message .= Lang::txt('Approve Link:') . ' ' . $adminLink . "\n";
$message .= "--------------------------------------------\n\n";

$message .= "\t" . Lang::txt('Module Content:') . "\n" . $this->object->get('content');

$message .= "\n\n";
$message .= $groupLink . "\n";

echo $message;
