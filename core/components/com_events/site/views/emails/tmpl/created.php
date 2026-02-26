<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id);

$message  = Lang::txt('EVENTS_CAL_LANG_ACT_ADDED_BY', $this->user->get('name'), $this->user->get('username'));
$message .= '' . "\n";
$message .= '' . "\n";
$titleTxt = Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE');
$descTxt = Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION');
$titleVal = html_entity_decode(strip_tags(stripslashes($this->row->title)));
$descVal = html_entity_decode(strip_tags(stripslashes($this->row->content)));
$message .= $titleTxt . ': ' . $titleVal . "\n\n";
$message .= $descTxt . ': ' . $descVal . "\n\n";
$message .= '' . "\n";
$message .= Request::base() . ltrim($sef, '/') . "\n";

echo $message;
