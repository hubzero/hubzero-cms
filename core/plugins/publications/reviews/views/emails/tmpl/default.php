<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews');

$message  = Lang::txt('PLG_PUBLICATIONS_REVIEWS_SOMEONE_POSTED_REVIEW') . "\n\n";
$message .= stripslashes($this->publication->title) . "\n\n";
$message .= Lang::txt('PLG_PUBLICATIONS_REVIEWS_TO_VIEW_COMMENT') . "\n";
$message .= Request::base() . '/' . ltrim($sef, '/') . "\n";

echo $message;
