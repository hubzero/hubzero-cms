<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator')
{
	$base = rtrim(substr($base, 0, strlen($base)-13), '/');
	$sef = 'support/ticket/' . $this->ticket->get('id');
}
else
{
	$sef = Route::url($this->ticket->link());
}
$link = $base . '/' . trim($sef, '/');

// Build message
$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . $base . '/support / Ticket #' . $this->ticket->get('id') . "\n";
}

$message .= '----------------------------'."\n";

if (!$this->config->get('email_terse'))
{
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')).': '.$this->ticket->get('id')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')).': '.$this->ticket->get('summary')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')).': '.$this->ticket->get('created')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter->get('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_STATUS')).': '.$this->ticket->status->get('title')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_TAGS')).': '.$this->ticket->tags('string')."\n";
	$message .= '----------------------------'."\n\n";
	if ($this->comment->isPrivate())
	{
		$message .= '!! ' . Lang::txt('COM_SUPPORT_COMMENT_PRIVATE') . " !!\n";
	}
	$message .= Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_POSTED', $this->ticket->get('id')) . ': ' . $this->comment->creator->get('name') . '(' . $this->comment->creator->get('username') . ")\n";
	$message .= Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_CREATED') . ': ' . $this->comment->get('created') . "\n\n";
	if ($this->comment->changelog()->lists())
	{
		foreach ($this->comment->changelog()->lists() as $type => $log)
		{
			if (is_array($log) && count($log) > 0)
			{
				foreach ($log as $items)
				{
					if ($type == 'changes')
					{
						$message .= ' * ' . Lang::txt('COM_SUPPORT_CHANGELOG_BEFORE_AFTER', $items->field, $items->before, $items->after) . "\n";
					}
					else if ($type == 'notifications')
					{
						$message  .= ' * ' . Lang::txt('COM_SUPPORT_CHANGELOG_NOTIFIED', $items->role, $items->name, $items->address) . "\n";
					}
				}
				$message .= "\n";
			}
		}
	}
	$message .= preg_replace("/<br\s?\/>/i", '', $this->comment->get('comment'));
	if ($this->comment->attachments->count() > 0)
	{
		$message .= "\n\n";
		foreach ($this->comment->attachments as $attachment)
		{
			$message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
		}
	}
}
else
{
	$message .= Lang::txt('COM_SUPPORT_NOTIFY_TICKET_UPDATED', $this->ticket->get('id'), $link) . "\n";
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
