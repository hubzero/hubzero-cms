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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

$emailbody  = JText::_('COM_STORE_THANKYOU') . ' ' . JText::_('COM_STORE_IN_THE') . ' ' . $this->sitename . ' ' . JText::_(strtolower($this->option)) . '!' . "\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_KEEP') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= ' ' . JText::_('COM_STORE_ORDER') . ' ' . JText::_('COM_STORE_NUM') . ': ' . $this->orderid . "\n";
$emailbody .= ' ' . JText::_('ORDER') . ' ' . JText::_('TOTAL') . ': ' . $this->cost . ' ' . JText::_('POINTS') . "\n";
$emailbody .= ' ' . JText::_('PLACED') . ' ' . JHTML::_('date', $this->now, $dateFormat, $tz) . "\n";
$emailbody .= ' ' . JText::_('STATUS') . ': ' . JText::_('RECEIVED') . "\n";
$emailbody .= '----------------------------------------------------------' . "\n";
$emailbody .= $this->details . "\n";
$emailbody .= '----------------------------------------------------------' . "\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_ORDER_PROCESSED') . '. ';
$emailbody .= JText::_('COM_STORE_EMAIL_QUESTIONS') . '.' . "\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_THANKYOU') . "\n";

echo $emailbody;
