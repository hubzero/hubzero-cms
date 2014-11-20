<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();

// Build link to wish
$base = rtrim($juri->base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');
$link = $base . '/' . ltrim(JRoute::_($this->wish->link()), '/');

// Get author name
$name  = $this->wish->proposer('name', JText::_('COM_WISHLIST_UNKNOWN'));
$login = $this->wish->proposer('username', JText::_('COM_WISHLIST_UNKNOWN'));

if ($this->wish->get('anonymous'))
{
	$name  = JText::_('COM_WISHLIST_ANONYMOUS');
	$login = JText::_('COM_WISHLIST_ANONYMOUS');
}

// Build message
$message  = '----------------------------' . "\n";
$message .= JText::_('COM_WISHLIST_WISH') . ' #' . $this->wish->get('id') . ', ' . $this->wishlist->get('title') . ' ' . JText::_('COM_WISHLIST_WISHLIST') . "\n";
$message .= JText::_('COM_WISHLIST_WISH_DETAILS_SUMMARY') . ': ' . stripslashes($this->wish->get('subject')) . "\n";
$message .= JText::_('COM_WISHLIST_PROPOSED_ON') . ' ' . $this->wish->proposed() . "\n";
$message .= JText::_('COM_WISHLIST_BY') . ' ' . $name . ' ' . ($this->wish->get('anonymous') ? '' : '(' . $login . ' )') . "\n";
$message .= '----------------------------' . "\n\n";

// Get author name
$name  = $this->comment->creator('name', JText::_('COM_WISHLIST_UNKNOWN'));
$login = $this->comment->creator('username', JText::_('COM_WISHLIST_UNKNOWN'));

if ($this->comment->get('anonymous'))
{
	$name  = JText::_('COM_WISHLIST_ANONYMOUS');
	$login = JText::_('COM_WISHLIST_ANONYMOUS');
}

$message .= JText::_('COM_WISHLIST_MSG_COMMENT_BY') . ' ' . $name . ' ';
$message .= $this->comment->get('anonymous') ? '' : '(' . $login . ')';
$message .= ' ' . JText::_('COM_WISHLIST_MSG_POSTED_ON').' '. $this->comment->created() . ':' . "\r\n";
$message .= $this->comment->content('clean') . "\r\n";
$message .= $this->comment->get('attachment');

$message .= "\n\n";
$message .= JText::_('COM_WISHLIST_GO_TO') . ' ' . $link . ' ' . JText::_('COM_WISHLIST_TO_VIEW_THIS_WISH') . '.';

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message);
