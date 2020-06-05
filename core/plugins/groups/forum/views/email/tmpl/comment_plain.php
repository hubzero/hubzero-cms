<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
$sef  = Route::url($this->thread->link());
$link = $base . '/' . trim($sef, '/');

// Build message
$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= Lang::txt('PLG_GROUPS_FORUM_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . $base . ' / ' . Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD', $this->thread->get('id')) . "\n";
}
$message .= ($this->post->get('anonymous')) ? Lang::txt('JANONYMOUS') : $this->post->creator->get('name') . ' (' . $this->post->creator->get('username') . ')';
$message .= ' wrote (in ' . $this->group->get('description') . ': ' . $this->section->get('title') . ' - ' . $this->category->get('title') . ' - ' . $this->thread->get('title') . '):';

$output = html_entity_decode(strip_tags($this->post->content), ENT_COMPAT, 'UTF-8');
$output = preg_replace_callback(
	"/(&#[0-9]+;)/",
	function($m)
	{
		return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
	},
	$output
);

$message .= $output;

$attachments = $this->post->attachments()
	->whereIn('state', array(Components\Forum\Models\Post::STATE_PUBLISHED))
	->rows();
if ($attachments->count() > 0)
{
	$message .= "\n\n";
	foreach ($attachments as $attachment)
	{
		$message .= $base . '/' . trim(Route::url($this->thread->link()), '/') . '/' . $attachment->get('post_id') . '/' . $attachment->get('filename') . "\n";
	}
}

if ($this->unsubscribe)
{
	$message .= "\n\n" . Lang::txt('PLG_GROUPS_FORUM_EMAIL_UNSUBSCRIBE') . ":\n" . $this->get('unsubscribe');
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
