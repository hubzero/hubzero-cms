<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$sef = Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews');

$message  = Lang::txt('PLG_RESOURCES_REVIEWS_SOMEONE_POSTED_REVIEW') . "\r\n\r\n";
$message .= '----------------------------' . "\r\n";
$message .= Lang::txt('Resource:') . ' #' . $this->resource->id . ' - ' . stripslashes($this->resource->title) . "\r\n";
$message .= Lang::txt('Review posted on:') . ' ' . Date::of($this->review->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\r\n";
$message .= '----------------------------' . "\r\n\r\n";
$message .= preg_replace('#<br[\s/]?>#', "\r", strip_tags($this->review->comment)) . "\r\n\r\n";
$message .= Lang::txt('PLG_RESOURCES_REVIEWS_TO_VIEW_COMMENT') . "\r\n";
$message .= rtrim(Request::base(), '/') . '/' . ltrim($sef, '/') . "\r\n";

echo $message;
