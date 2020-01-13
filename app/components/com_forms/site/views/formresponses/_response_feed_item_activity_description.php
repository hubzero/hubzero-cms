<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->item;
$activity = $item->get('description');
$activityUpper = strtoupper($activity);
$activityDescription = Lang::txt("COM_FORMS_FEED_ACTIVITY_DESCRIPTION_$activityUpper");

echo $activityDescription;
