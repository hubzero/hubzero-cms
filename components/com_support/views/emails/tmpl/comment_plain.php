<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();
$jconfig = JFactory::getConfig();

if (!($this->ticket instanceof SupportModelTicket))
{
	$this->ticket = new SupportModelTicket($this->ticket);
}
if (!($this->comment instanceof SupportModelComment))
{
	$this->comment = new SupportModelComment($this->comment);
}

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/' . $this->ticket->get('id');
}
else
{
	$sef = JRoute::_($this->ticket->link());
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

// Build message
$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= JText::_('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . rtrim($juri->base(), DS) . '/support / Ticket #' . $this->ticket->get('id') . "\n";
}
$message .= '----------------------------'."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET')).': '.$this->ticket->get('id')."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET_DETAILS_SUMMARY')).': '.$this->ticket->get('summary')."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET_DETAILS_CREATED')).': '.$this->ticket->get('created')."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET_DETAILS_STATUS')).': '.$this->ticket->status()."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_TICKET_DETAILS_TAGS')).': '.$this->ticket->tags('string')."\n";
$message .= '----------------------------'."\n\n";
$message .= JText::sprintf('COM_SUPPORT_TICKET_EMAIL_COMMENT_POSTED', $this->ticket->get('id')) . ': ' . $this->comment->creator('name') . '(' . $this->comment->creator('username') . ")\n";
$message .= JText::_('COM_SUPPORT_TICKET_EMAIL_COMMENT_CREATED') . ': ' . $this->comment->created() . "\n\n";
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
					$message .= ' * ' . JText::sprintf('COM_SUPPORT_CHANGELOG_BEFORE_AFTER', $items->field, $items->before, $items->after) . "\n";
				}
				else if ($type == 'notifications')
				{
					$message  .= ' * ' . JText::sprintf('COM_SUPPORT_CHANGELOG_NOTIFIED', $items->role, $items->name, $items->address) . "\n";
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
		$message .= $base . DS . trim(JRoute::_($attachment->link()), DS) . "\n";
	}
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
