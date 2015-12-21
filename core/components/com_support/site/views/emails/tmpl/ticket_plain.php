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

// No direct access.
defined('_HZEXEC_') or die();

if (!($this->ticket instanceof \Components\Support\Models\Ticket))
{
	$this->ticket = new \Components\Support\Models\Ticket($this->ticket);
}

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
	if ($this->ticket->submitter('id'))
	{
		jimport('joomla.user.helper');
		$usertype = implode(', ', \JUserHelper::getUserGroups($this->ticket->submitter('id')));
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
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE')).': '.$usertype."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL')).': '. $this->ticket->get('email') ."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_IP_HOSTNAME')).': '. $this->ticket->get('ip') .' ('.$this->ticket->get('hostname').')' ."\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_OS')).': '. $this->ticket->get('os') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_BROWSER')).': '. $this->ticket->get('browser') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_UAS')).': '. $this->ticket->get('uas') . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_COOKIES')).': ' . ($this->ticket->get('cookies') ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED') : Lang::txt('COM_SUPPORT_COOKIES_DISABLED')) . "\n";
	$message .= strtoupper(Lang::txt('COM_SUPPORT_REFERRER')).': '. $this->ticket->get('referrer') . "\n";
	$message .= '----------------------------'."\n\n";
	$message .= $this->ticket->content('clean');
	if ($this->ticket->attachments()->total() > 0)
	{
		$message .= "\n\n";
		foreach ($this->ticket->attachments() as $attachment)
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
