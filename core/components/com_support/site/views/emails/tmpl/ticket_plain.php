<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

$message = '';
if (!$this->config->get('email_terse'))
{
	$usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
	if ($this->ticket->submitter->get('id'))
	{
		$gids = array();
		foreach (User::getInstance($this->ticket->submitter->get('id'))->accessgroups() as $g)
		{
			$gids[] = $g->group_id;
		}
		$usertype = implode(', ', $gids);
	}

	if ($this->delimiter)
	{
		$message .= $this->delimiter . "\n";
		$message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
		$message .= 'Message from ' . rtrim(Request::base(), '/') . '/support / Ticket #' . $this->ticket->get('id') . "\n";
	}
	$message .= '----------------------------'."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')).': '.$this->ticket->get('id')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')).': '.$this->ticket->get('summary')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')).': '.$this->ticket->get('created')."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter->get('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE')).': '.$usertype."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL')).': '. $this->ticket->get('email') ."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_IP_HOSTNAME')).': '. $this->ticket->get('ip') .' ('.$this->ticket->get('hostname').')' ."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_OS')).': '. $this->ticket->get('os') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_BROWSER')).': '. $this->ticket->get('browser') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_UAS')).': '. $this->ticket->get('uas') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_COOKIES')).': ' . ($this->ticket->get('cookies') ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED') : Lang::txt('COM_SUPPORT_COOKIES_DISABLED')) . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_REFERRER')).': '. $this->ticket->get('referrer') . "\n";
	$message .= '----------------------------'."\n\n";
	$message .= $this->ticket->get('report');
	if ($this->ticket->attachments->count() > 0)
	{
		$message .= "\n\n";
		foreach ($this->ticket->attachments as $attachment)
		{
			$message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
		}
	}
}
else
{
	$message .= Lang::txt('COM_SUPPORT_NOTIFY_TICKET_CREATED', $this->ticket->get('id'), $link) . "\n";
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
