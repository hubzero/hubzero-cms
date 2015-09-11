<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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