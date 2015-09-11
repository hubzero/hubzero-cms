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

$sef = Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&active=members');

$message  = Lang::txt('COM_GROUPS_JOIN_REQUEST_EMAIL_DETAILS',$this->sitename)."\n\n";
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
