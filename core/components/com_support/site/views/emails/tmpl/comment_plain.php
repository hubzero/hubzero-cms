<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!($this->ticket instanceof \Components\Support\Models\Ticket))
{
	$this->ticket = new \Components\Support\Models\Ticket($this->ticket);
}
if (!($this->comment instanceof \Components\Support\Models\Comment))
{
	$this->comment = new \Components\Support\Models\Comment($this->comment);
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

// Build message
$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . $base . '/support / Ticket #' . $this->ticket->get('id') . "\n";
}
$message .= '----------------------------'."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')).': '.$this->ticket->get('id')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')).': '.$this->ticket->get('summary')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')).': '.$this->ticket->get('created')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_STATUS')).': '.$this->ticket->status()."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_TAGS')).': '.$this->ticket->tags('string')."\n";
$message .= '----------------------------'."\n\n";
if ($this->comment->isPrivate())
{
	$message .= '!! ' . Lang::txt('COM_SUPPORT_COMMENT_PRIVATE') . " !!\n";
}
$message .= Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_POSTED', $this->ticket->get('id')) . ': ' . $this->comment->creator('name') . '(' . $this->comment->creator('username') . ")\n";
$message .= Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_CREATED') . ': ' . $this->comment->created() . "\n\n";
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
$message .= $this->comment->content('clean');
if ($this->comment->attachments()->total() > 0)
{
	$message .= "\n\n";
	foreach ($this->comment->attachments() as $attachment)
	{
		$message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
	}
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
