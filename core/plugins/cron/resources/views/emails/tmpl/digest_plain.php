<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
