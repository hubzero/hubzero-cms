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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();

$sef = JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews');

$message  = JText::_('PLG_RESOURCES_REVIEWS_SOMEONE_POSTED_REVIEW') . "\r\n\r\n";
$message .= '----------------------------' . "\r\n";
$message .= JText::_('Resource:') . ' #' . $this->resource->id . ' - ' . stripslashes($this->resource->title) . "\r\n";
$message .= JText::_('Review posted on:') . ' ' . JHTML::_('date', $this->review->created, JText::_('DATE_FORMAT_HZ1')) . "\r\n";
$message .= '----------------------------' . "\r\n\r\n";
$message .= preg_replace('#<br[\s/]?>#', "\r", strip_tags($this->review->comment)) . "\r\n\r\n";
$message .= JText::_('PLG_RESOURCES_REVIEWS_TO_VIEW_COMMENT') . "\r\n";
$message .= rtrim($juri->base(), DS) . DS . ltrim($sef, DS) . "\r\n";

echo $message;
