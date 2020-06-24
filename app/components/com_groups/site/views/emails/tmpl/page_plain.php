<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base      = rtrim(str_replace('administrator', '', Request::base()), '/');
$groupLink = $base . '/groups/' . $this->group->get('cn');
$adminLink = $base . '/administrator/index.php?option=com_groups&gid=' . $this->group->get('cn') . '&controller=pages';

// get page object
$page = new \Components\Groups\Models\Page($this->object->get('id'));

$message  = Lang::txt('Group Pages') . "\n\n";
$message .= "--------------------------------------------\n";
$message .= Lang::txt('Group:') . ' ' . strip_tags($this->group->get('description')) . "\n";
$message .= Lang::txt('Page Title:') . ' ' . $page->get('title') . "\n";
$message .= Lang::txt('Page ID:') . ' #' . $page->get('id') . "\n";
$message .= Lang::txt('Created:') . ' @ ' . Date::of($page->version()->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of($page->version()->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";

$profile = User::getInstance($page->version()->get('created_by'));
$message .= Lang::txt('Created By:') . ' ' . $profile->get('name') . ' ('.$profile->get('username').')' . "\n";
$message .= Lang::txt('Status:') . ' ' . ($page->version()->get('approved') == 1) ? Lang::txt('Approved') : Lang::txt('Unapproved') . "\n";
$message .= Lang::txt('Page Link:') . ' ' . $groupLink . '/' . $this->object->get('alias') . "\n";
$message .= Lang::txt('Approve Link:') . ' ' . $adminLink . "\n";
$message .= "--------------------------------------------\n\n";

$message .= "\t" . Lang::txt('Page Content:') . "\n" . $page->version()->get('content');

$message .= "\n\n";
$message .= $groupLink . "\n";

echo $message;
