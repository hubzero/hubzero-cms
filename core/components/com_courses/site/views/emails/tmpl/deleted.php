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
 * @var \Hubzero\Component\View&object{course: mixed, gcn: mixed, msg: mixed, user: mixed} $this
 */

$sef = ltrim(Route::url('index.php?option=' . $this->option . '&gid=' . $this->course->get('cn')), '/');

$message  = Lang::txt('COURSES_USER_HAS_DELETED_COURSE', $this->gcn, $this->user->get('username')) . "\n\n";
if ($this->msg) {
    $message .= stripslashes($this->msg) . "\n\n";
}
$message .= Lang::txt('COURSES_EMAIL_USER_IF_QUESTIONS', $this->user->get('username'), $this->user->get('email'))
    . "\n";
$message .= rtrim(Request::base(), '/') . '/' . $sef . "\n";

echo $message;
