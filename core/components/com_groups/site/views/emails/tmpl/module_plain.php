<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
