<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->event->id);

$message  = Lang::txt('EVENTS_CAL_LANG_ACT_DELETED_BY', $this->user->get('name'), $this->user->get('login'));
$message .= '' . "\n";
$message .= '' . "\n";
$titleTxt = Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE');
$descTxt = Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION');
$titleVal = html_entity_decode(strip_tags(stripslashes($this->event->title)));
$descVal = html_entity_decode(strip_tags(stripslashes($this->event->content)));
$message .= $titleTxt . ': ' . $titleVal . "\n";
$message .= $descTxt . ': ' . $descVal . "\n";
$message .= '' . "\n";
//$message .= Request::base() . ltrim($sef, '/')."\n";

echo $message;
