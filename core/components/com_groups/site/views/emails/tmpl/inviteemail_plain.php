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

$sef = ltrim(Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn')), '/');

$message  = Lang::txt('COM_GROUPS_INVITE_EMAIL_INVITED_BY', $this->user->get('name'), $this->sitename)."\n\n";
$message .= Lang::txt('COM_GROUPS_GROUP').': '.$this->group->get('description')."\n\n";

if ($this->msg)
{
	$message .= '====================='."\n";
	$message .= stripslashes($this->msg)."\n";
	$message .= '====================='."\n\n";
}

$message .= "If you already have a registered account on ".$this->sitename.", click or copy and paste the link below into a browser window. \r\n";
$sef = ltrim(Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&task=accept&token='.$this->token), '/');

$message .= rtrim(Request::base(), '/') . '/' . $sef."\n\n";
$message .= "--------------------------------------------\n\n";

//$message .= "If you do not have an account on ".$this->sitename.", you must first click or copy and paste the first link, where you will register for an account. Then you must come back to click or copy and paste link two. \n\n";

//$sef = Route::url('index.php?option=com_members&controller=register');
//if (substr($sef,0,1) == '/') {
//	$sef = substr($sef,1,strlen($sef));
//}
//$message .= "1. ".Request::base().$sef."\n\n";

//$sef = Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn').'&task=accept&token='.$this->token);
//if (substr($sef,0,1) == '/') {
//	$sef = substr($sef,1,strlen($sef));
//}
//$message .= "2. ".Request::base().$sef."\n\n";

$message .= "If you DO NOT have an account on " . $this->sitename . ", please follow this link! \n\n";

$return = DS . "groups" . DS . $this->group->get('cn') . DS . "accept?token=" . $this->token;
//$return = DS . "groups" . DS . $this->group->get('cn') . DS . "accept";

$sef = ltrim(Route::url('index.php?option=com_members&controller=register'), '/');

$message .= rtrim(Request::base(), '/') . '/' . $sef . "?return=".base64_encode($return)."\n\n\n";

$message .= "--------------------------------------------\n\n";
$message .= Lang::txt('COM_GROUPS_INVITE_EMAIL_QUESTIONS', $this->user->get('name'), $this->user->get('email'))."\n";

echo $message;
