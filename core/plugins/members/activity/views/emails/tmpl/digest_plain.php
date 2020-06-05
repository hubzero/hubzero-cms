<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

	$name = Lang::txt('JANONYMOUS');

	if (!$row->log->get('anonymous'))
	{
		$creator = User::getInstance($row->log->get('created_by'));
		$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN'))));
	}

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
