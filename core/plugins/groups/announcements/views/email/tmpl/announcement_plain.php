<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// get the group
$group = \Hubzero\User\Group::getInstance($this->announcement->get('scope_id'));
$groupLink = rtrim(Request::base(), '/') . '/groups/' . $group->get('cn');

echo Lang::txt('Group Announcement') . ' - ' . $group->get('description') . "\n";
echo '-------------------------------------------------------' . "\n\n";
echo strip_tags($this->announcement->get('content'))  . "\n\n";
echo $groupLink . '/announcements';
