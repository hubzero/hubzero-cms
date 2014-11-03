<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juri    = JURI::getInstance();
$jconfig = JFactory::getConfig();
$base    = rtrim($juri->base(), DS);
if (substr($base, -13) == 'administrator')
{
	$base   = substr($base, 0, strlen($base)-13);
	$sef    = 'projects/' . $this->project->alias;
	$pubSef = 'publications' . DS . $this->row->publication_id . DS . $this->row->version_number;
}
else
{
	$sef    = JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias);
	$pubSef = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->row->publication_id . '&v=' . $this->row->version_number);
}

$link 	 = rtrim($base, DS) . DS . trim($sef, DS);
$pubLink = rtrim($base, DS) . DS . trim($pubSef, DS);

$message  = $this->subject."\n";
$message .= '-------------------------------'."\n";
$message .= JText::_('COM_PUBLICATIONS_PUBLICATION').' "'.$this->row->title.'" (id #'.$this->row->publication_id.')'."\n";
$message .= JText::_('COM_PUBLICATIONS_EMAIL_URL').': ' . $pubLink . "\n";
$message .= '-------------------------------'."\n";
$message .= JText::_('COM_PUBLICATIONS_PROJECT').': '.$this->project->title.' ('.$this->project->alias.', id #'.$this->project->id.')'."\n";
$message .= "\n";
$message .= JText::_('COM_PUBLICATIONS_EMAIL_PROJECT_URL') . ': ' . $link . "\n";
$message .= '-------------------------------'."\n";

// Append a message
if ($this->message)
{
	$message .= JText::_('COM_PUBLICATION_MSG_MESSAGE_FROM_ADMIN') . ': ' . "\n";
	$message .= $this->message ."\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;