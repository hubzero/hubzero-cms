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

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{gcn: mixed, group: mixed, msg: mixed, user: mixed} $this
 */

$sef = ltrim(Route::url('index.php?option=' . $this->option . '&cname=' . $this->group->get('cn')), '/');

$message  = Lang::txt('COM_GROUPS_DELETE_BY_MEMBER', $this->gcn, $this->user->get('username')) . "\n\n";
if ($this->msg) {
    $message .= stripslashes($this->msg) . "\n\n";
}
$message .= Lang::txt('COM_GROUPS_DELETE_QUESTIONS', $this->user->get('username'), $this->user->get('email')) . "\n";
$message .= rtrim(Request::base(), '/') . '/' . $sef . "\n";

echo $message;
