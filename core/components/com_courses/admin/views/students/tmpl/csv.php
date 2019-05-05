<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=registrations.csv");
header("Pragma: no-cache");
header("Expires: 0");

foreach ($this->rows as $row)
{

	$section = \Components\Courses\Models\Section::getInstance($row->get('section_id'));

	echo encodeCSVField($row->get('user_id'));
	echo ',';
	echo encodeCSVField($row->get('name'));
	echo ',';
	echo encodeCSVField($row->get('email'));
	echo ',';
	echo encodeCSVField($section->exists()) ? $this->escape(stripslashes($section->get('title'))) : Lang::txt('COM_COURSES_NONE');
	echo ',';
	if ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00') {
		echo encodeCSVField(Date::of($row->get('enrolled'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')));
	}
	else {
		echo encodeCSVField(Lang::txt('COM_COURSES_UNKNOWN'));
	}
	echo "\n";

}

die;

function encodeCSVField($string)
{
	if (strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false)
	{
		$string = '"' . str_replace('"', '""', $string) . '"';
	}
	return $string;
}
