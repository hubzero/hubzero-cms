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
$base = str_replace('/administrator', '', $base);
$sef  = Route::urlForClient('site', $this->member->link());
$link = $base . '/' . trim($sef, '/');

// Build message
$message  = Lang::txt('PLG_CRON_RESOURCES_EMAIL_MEMBERS_EXPLANATION', $link, $this->member->get('name') . ' (' . $this->member->get('username') . ')');

foreach ($this->rows as $row)
{
	$content = '';
	if ($row->get('introtext'))
	{
		$content = $row->get('introtext');
	}
	else if ($row->get('fulltxt'))
	{
		$content = $row->get('fulltxt');
		$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);
		$content = trim($content);
	}

	$content = html_entity_decode(strip_tags($content), ENT_COMPAT, 'UTF-8');
	$content = preg_replace_callback(
		"/(&#[0-9]+;)/",
		function($m)
		{
			return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
		},
		$content
	);

	$message .= '------------' . "\n";
	$message .= stripslashes($row->get('title')) . "\n\n";
	$message .= $content . "\n\n";
	$message .= $base . Route::urlForClient('site', $row->link(), false) . "\n\n";
}

$message .= Lang::txt('PLG_CRON_RESOURCES_EMAIL_MEMBERS_MORE', Config::get('sitename'));
$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n";
