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

$laststep = (count($this->steps) - 1);

$html  = '<ol id="steps">'."\n";
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
$html .= '</ol>'."\n";
$html .= '<div class="clear"></div>'."\n";

echo $html;
?>