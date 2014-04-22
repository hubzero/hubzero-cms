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

$st = new SupportTags(JFactory::getDBO());

$base = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base = substr($base, 0, strlen($base)-13);
	$sef = 'support/ticket/' . $this->ticket->id;
}
else
{
	$sef = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket->id);
}
$link = rtrim($base, DS) . DS . trim($sef, DS);

$this->commentor = JFactory::getUser($this->comment->created_by);

// Build message
$message = '';
if ($this->delimiter) 
{
	$message .= $this->delimiter . "\n";
	$message .= 'You can reply to this message, just include your reply text above this area' . "\n";
	$message .= 'Attachments (up to 2MB each) are permitted' . "\n";
	$message .= 'Message from ' . rtrim($juri->base(), DS) . '/support / Ticket #' . $this->ticket->id . "\n";
}
$message .= '----------------------------'."\n";
$message .= strtoupper(JText::_('TICKET')).': '.$this->ticket->id."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_SUMMARY')).': '.$this->ticket->summary."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED')).': '.$this->ticket->created."\n";
$message .= strtoupper(JText::_('TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->name . ($this->ticket->login ? ' ('.$this->ticket->login.')' : '') . "\n";
$message .= strtoupper(JText::_('TICKET_FIELD_STATUS')).': '.SupportHtml::getStatus($this->ticket->status)."\n";
$message .= strtoupper(JText::_('Tags')).': '.$st->get_tag_string($this->ticket->id, 0, 0, NULL, 0, 1)."\n";
$message .= '----------------------------'."\n\n";
$message .= JText::sprintf('TICKET_EMAIL_COMMENT_POSTED', $this->ticket->id) . ': ' . $this->commentor->get('name') . '(' . $this->comment->created_by . ")\n";
$message .= JText::_('TICKET_EMAIL_COMMENT_CREATED') . ': ' . $this->comment->created . "\n\n";
if ($this->comment->changelog) 
{
	foreach ($this->comment->changelog as $type => $log)
	{
		if (is_array($log) && count($log) > 0)
		{
			foreach ($log as $items)
			{
				if ($type == 'changes')
				{
					$message .= ' * ' . $items['field'] . ' changed from "' . $items['before'] . '" to "' . $items['after'] . '"' . "\n";
				}
				else if ($type == 'notifications')
				{
					$message  .= ' * ' . JText::_('Messaged') . ' (' . $items['role'] . ') ' . $items['name'] . ' - ' . $items['address'] . "\n";
				}
			}
			$message .= "\n";
		}
	}
}
$message .= $this->attach->parse($this->comment->comment);
$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
