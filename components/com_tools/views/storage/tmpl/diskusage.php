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
defined('_JEXEC') or die( 'Restricted access' );

$html = '';
if (!$this->ajax)
{
	$html .= '<dl id="diskusage">'."\n";
}
if ($this->writelink)
{
	$html .= "\t".'<dt>' . JText::_('COM_TOOLS_STORAGE') . ' (<a href="'.JRoute::_('index.php?option='.$this->option.'&task=storage').'">' . JText::_('COM_TOOLS_STORAGE_MANAGE') . '</a>)</dt>'."\n";
}
else
{
	$html .= "\t".'<dt>' . JText::_('COM_TOOLS_STORAGE') . '</dt>'."\n";
}
$html .= "\t".'<dd id="du-amount"><div class="du-amount-bar" style="width:'.$this->amt.'%;"><strong>&nbsp;</strong><span class="du-amount-text">'.$this->amt.'% of '.$this->total.'GB</span></div></dd>'."\n";
if ($this->msgs)
{
	if (count($this->du) <=1)
	{
		$html .= "\t".'<dd id="du-msg"><p class="error">' . JText::_('COM_TOOLS_STORAGE_ERROR_RETRIEVING') . '</p></dd>'."\n";
	}
	if ($this->percent == 100)
	{
		$html .= "\t".'<dd id="du-msg"><p class="warning">' . JText::_('COM_TOOLS_STORAGE_WARNING_REACHED_LIMIT') . ' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=storageexceeded').'">' . JText::_('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') . '</a>.</p></dd>'."\n";
	}
	if ($this->percent > 100)
	{
		$html .= "\t".'<dd id="du-msg"><p class="warning">' . JText::_('COM_TOOLS_STORAGE_WARNING_EXCEEDING_LIMIT') . ' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=storageexceeded').'">' . JText::_('COM_TOOLS_STORAGE_HOW_TO_RESOLVE') . '</a>.</p></dd>'."\n";
	}
}
if (!$this->ajax)
{
	$html .= '</dl>'."\n";
}
echo $html;