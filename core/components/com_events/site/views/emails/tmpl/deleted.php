<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option='.$this->option.'&task=details&id='.$this->event->id);

$message  = Lang::txt('EVENTS_CAL_LANG_ACT_DELETED_BY', $this->user->get('name'), $this->user->get('login'));
$message .= ''."\n";
$message .= ''."\n";
$message .= Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE').': '.html_entity_decode(strip_tags(stripslashes($this->event->title)))."\n";
$message .= Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION').': '.html_entity_decode(strip_tags(stripslashes($this->event->content)))."\n";
$message .= ''."\n";
//$message .= Request::base() . ltrim($sef, '/')."\n";

echo $message;
