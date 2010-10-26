<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$laststep = (count($this->steps) - 1);

$html  = '<div id="steps_div"><ol id="steps">'."\n";
if ($this->progress['submitted'] == 1) {
	$html .= "\t".'<li id="start"><a href="'. JRoute::_('index.php?option=com_resources&id='.$this->id) .'">'.JText::_('COM_CONTRIBUTE_START').'</a></li>'."\n";
} else {
	$html .= "\t".'<li id="start"><a href="'. JRoute::_('index.php?option='.$this->option) .'">'.JText::_('COM_CONTRIBUTE_START').'</a></li>'."\n";
}
for ($i=1, $n=count( $this->steps ); $i < $n; $i++) 
{
	$html .= "\t".'<li';
	if ($this->step == $i) { 
		$html .= ' class="active"';
	} elseif ($this->progress[$this->steps[$i]] == 1) {
		$html .= ' class="completed"';
	}
	$html .= '>';
	if ($this->step == $i) {
		$html .= $this->steps[$i];
	} elseif ($this->progress[$this->steps[$i]] == 1) {
		$html .= '<a href="'. JRoute::_('index.php?option='.$this->option.'&step='.$i.'&id='.$this->id) .'">'.JText::_('COM_CONTRIBUTE_STEP_'.strtoupper($this->steps[$i])).'</a>';
	} else {
		if ($this->progress['submitted'] == 1) {
			$html .= '<a href="'. JRoute::_('index.php?option='.$this->option.'&step='.$i.'&id='.$this->id) .'">'.JText::_('COM_CONTRIBUTE_STEP_'.strtoupper($this->steps[$i])).'</a>';
		} else {
			$html .= $this->steps[$i];
		}
	}
	$html .= '</li>'."\n";
}
if ($this->progress['submitted'] != 1) {
	$html .= "\t".'<li id="trash"';
	if ($this->step == 'discard') { 
		$html .= ' class="active">'.JText::_('COM_CONTRIBUTE_CANCEL'); 
	} else {
		$html .= '><a href="'.JRoute::_('index.php?option='.$this->option.'&task=discard');
		$html .= ($this->id) ? '&amp;id='.$this->id : '';
		$html .= '">'.JText::_('COM_CONTRIBUTE_CANCEL').'</a>';
	}
	$html .= '</li>'."\n";
}
$html .= '</ol></div>'."\n";
$html .= '<div class="clear"></div>'."\n";

echo $html;
?>