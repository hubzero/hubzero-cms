<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
