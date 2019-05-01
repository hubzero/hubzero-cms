<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$creator = User::getInstance($this->resource->created_by);

$type = $this->resource->type;

$link = rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->resource->link()), '/');

$message  = Lang::txt('COM_RESOURCES_NEW_SUBMISSION') . "\n";
$message .= '----------------------------' . "\n";
$message .= strtoupper(Lang::txt('COM_RESOURCES_ID')) . ' #' . $this->resource->id . "\n";
$message .= strtoupper(Lang::txt('COM_RESOURCES_TYPE')) . ': ' . $type->type . "\n";
$message .= strtoupper(Lang::txt('COM_RESOURCES_CREATED')) . ': ' . $this->resource->created . "\n";
$message .= strtoupper(Lang::txt('COM_RESOURCES_CREATOR')) . ': ' . $creator->get('name') . "\n";
$message .= '----------------------------' . "\n\n";
$message .= strtoupper(Lang::txt('COM_RESOURCES_TITLE')) . ': ' . $this->resource->title . "\n\n";
$message .= $this->resource->introtext . "\n\n";
$message .= 'To view the submission and take actions, go to: ' . "\n";
$message .= $link . "\n";

echo $message;
