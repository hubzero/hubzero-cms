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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
