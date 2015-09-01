<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

$profile = \Hubzero\User\Profile::getInstance($page->version()->get('created_by'));
$message .= Lang::txt('Created By:') . ' ' . $profile->get('name') . ' ('.$profile->get('username').')' . "\n";
$message .= Lang::txt('Status:') . ' ' . ($page->version()->get('approved') == 1) ? Lang::txt('Approved') : Lang::txt('Unapproved') . "\n";
$message .= Lang::txt('Page Link:') . ' ' . $groupLink . '/' . $this->object->get('alias') . "\n";
$message .= Lang::txt('Approve Link:') . ' ' . $adminLink . "\n";
$message .= "--------------------------------------------\n\n";

$message .= "\t" . Lang::txt('Page Content:') . "\n" . $page->version()->get('content');

$message .= "\n\n";
$message .= $groupLink . "\n";

echo $message;
