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

$usertype = JText::_('Unknown');

$submitter = JUser::getInstance($this->ticket->login);
if ($submitter && is_object($submitter))
{
	if (version_compare(JVERSION, '1.6', 'lt'))
	{
		$usertype = $submitter->get('usertype');
	}
	else
	{
		jimport( 'joomla.user.helper' );
		$usertype = implode(', ', JUserHelper::getUserGroups($submitter->get('uidNumber')));
	}
}

$comment = $this->ticket->report;

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
$message .= strtoupper(JText::_('TICKET_DETAILS_USERTYPE')).': '.$usertype."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_EMAIL')).': '. $this->ticket->email ."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_IP_HOSTNAME')).': '. $this->ticket->ip .' ('.$this->ticket->hostname.')' ."\n";
$message .= strtoupper(JText::_('COM_SUPPORT_OS')).': '. $this->ticket->os . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_BROWSER')).': '. $this->ticket->browser . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_UAS')).': '. $this->ticket->uas . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_COOKIES')).': ' . ($this->ticket->cookies ? JText::_('COM_SUPPORT_COOKIES_ENABLED') : JText::_('COM_SUPPORT_COOKIES_DISABLED')) . "\n";
$message .= strtoupper(JText::_('COM_SUPPORT_REFERRER')).': '. $this->ticket->referrer . "\n";
$message .= '----------------------------'."\n\n";
$message .= $this->attach->parse($comment);

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
