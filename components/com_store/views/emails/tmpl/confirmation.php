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

$emailbody  = JText::_('COM_STORE_THANKYOU').' '.JText::_('COM_STORE_IN_THE').' '.$this->hubShortName.' '.JText::_(strtolower($this->option)).'!'."\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_KEEP')."\n";
$emailbody .= '----------------------------------------------------------'."\n";
$emailbody .= '	'.JText::_('COM_STORE_ORDER').' '. JText::_('COM_STORE_NUM').': '. $this->orderid ."\n";
$emailbody .= ' '.JText::_('ORDER').' '.JText::_('TOTAL').': '. $this->cost.' '.JText::_('POINTS')."\n";
$emailbody .= ' '.JText::_('PLACED').' '. JHTML::_('date', $this->now, '%d %b, %Y')."\n";
$emailbody .= ' '.JText::_('STATUS').': '.JText::_('RECEIVED')."\n";
$emailbody .= '----------------------------------------------------------'."\n";
$emailbody .= $this->details."\n";
$emailbody .= '----------------------------------------------------------'."\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_ORDER_PROCESSED').'. ';
$emailbody .= JText::_('COM_STORE_EMAIL_QUESTIONS').'.'."\n\n";
$emailbody .= JText::_('COM_STORE_EMAIL_THANKYOU')."\n";
echo $emailbody;
?>