<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{option: mixed, publication: mixed} $this
 */

$sef = Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews');

$message  = Lang::txt('PLG_PUBLICATIONS_REVIEWS_SOMEONE_POSTED_REVIEW') . "\n\n";
$message .= stripslashes($this->publication->title) . "\n\n";
$message .= Lang::txt('PLG_PUBLICATIONS_REVIEWS_TO_VIEW_COMMENT') . "\n";
$message .= Request::base() . '/' . ltrim($sef, '/') . "\n";

echo $message;
