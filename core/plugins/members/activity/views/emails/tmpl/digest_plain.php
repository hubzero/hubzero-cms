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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$sef  = Route::url($this->member->link());
$link = $base . '/' . trim($sef, '/');

// Build message
$message  = Lang::txt('PLG_CRON_ACTIVITY_EMAIL_MEMBERS_EXPLANATION', $link, $this->member->get('name') . ' (' . $this->member->get('username') . ')');

foreach ($this->rows as $row)
{
	$output = html_entity_decode(strip_tags($row->log->get('description')), ENT_COMPAT, 'UTF-8');
	$output = preg_replace_callback(
		"/(&#[0-9]+;)/",
		function($m)
		{
			return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
		},
		$output
	);

	$creator = User::getInstance($row->log->get('created_by'));
	$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN'))));

	$dt = Date::of($row->get('created'));
	$ct = Date::of('now');

	$lapsed = $ct->toUnix() - $dt->toUnix();

	if ($lapsed < 30)
	{
		$timestamp = Lang::txt('PLG_MEMBERS_ACTIVITY_JUST_NOW');
	}
	elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
	{
		$timestamp = $dt->toLocal('M j, Y');
	}
	elseif ($lapsed > 86400)
	{
		$timestamp = $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
	}
	else
	{
		$timestamp = $dt->relative();
	}

	$message .= '------------' . "\n";
	$message .= $name . ' - ' . $timestamp . "\n";
	$message .= $output . "\n\n";
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
