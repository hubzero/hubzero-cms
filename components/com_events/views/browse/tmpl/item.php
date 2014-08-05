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

// get the timezone name from offset
$timezone = timezone_name_from_abbr('',$this->row->time_zone*3600, NULL);

$this->row->content = stripslashes($this->row->content);
$this->row->content = str_replace('<br />','',$this->row->content);
//$this->row->content = EventsHtml::encode_html($this->row->content);

//$fields = $config->getCfg('fields');
if (!empty($this->fields)) {
	for ($i=0, $n=count( $this->fields ); $i < $n; $i++)
	{
		// explore the text and pull out all matches
		array_push($this->fields[$i], EventsControllerEvents::parseTag($this->row->content, $this->fields[$i][0]));
		// clean the original text of any matches
		$this->row->content = str_replace('<ef:'.$this->fields[$i][0].'>'.end($this->fields[$i]).'</ef:'.$this->fields[$i][0].'>','',$this->row->content);
	}
	$this->row->content = trim($this->row->content);
}

$start_date = JHTML::_('date',$this->row->publish_up, 'd M Y', $timezone);
$stop_date  = JHTML::_('date',$this->row->publish_down, 'd M Y', $timezone);

$html  = "\t".'<li id="event'.$this->row->id.'">'."\n";
$html .= "\t\t".'<dl class="event-details">'."\n";
if ($start_date == $stop_date) {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt>'.JHTML::_('date',$this->row->publish_up, 'd M Y', $timezone).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.JHTML::_('date',$this->row->publish_up, 'g:i a T', $timezone).'&nbsp;' .'</dd>'."\n";
	$html .= "\t\t\t".'<dd class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$this->row->publish_down, 'g:i a T', $timezone).'&nbsp;'.'</dd>'."\n";
} else {
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="starttime">'.JHTML::_('date',$this->row->publish_up, JText::_('DATE_FORMAT_HZ1'), $timezone).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="starttime">'.JHTML::_('date',$this->row->publish_up, 'g:i a T', $timezone).'&nbsp;'.'</dd>'."\n";
	if ($this->showdate) {
		$html .= "\t\t\t".'<dt class="endtime">'.strtolower(JText::_('EVENTS_CAL_LANG_TO')).' '.JHTML::_('date',$this->row->publish_down, JText::_('DATE_FORMAT_HZ1'), $timezone).'</dt>'."\n";
	}
	$html .= "\t\t\t".'<dd class="endtime">'.JHTML::_('date',$this->row->publish_down, 'g:i a T', $timezone).'&nbsp;'.'</dd>'."\n";
}
$html .= "\t\t".'</dl><div class="ewrap">'."\n";
$html .= "\t\t".'<p class="title"><a href="'. JRoute::_('index.php?option='.$this->option.'&task=details&id='.$this->row->id) .'">'. $this->escape(stripslashes($this->row->title)) .'</a></p>'."\n";
$html .= "\t\t".'<p class="category"><strong>Category:</strong> '. stripslashes($this->categories[$this->row->catid]) .'</p>'."\n";
$info = '';
foreach ($this->fields as $field)
{
	if ($field[4] == 1 && end($field) != '') {
		$info .= "\t\t".'<p class="'.$field[0].'"><strong>'. $field[1] .':</strong> '. end($field) .'</p>'."\n";
	}
}
$html .= $info;
if (!$info) {
	$html .= "\t\t".'<p class="description">'. \Hubzero\Utility\String::truncate($this->row->content,300) .'</p>'."\n";
}
$html .= "\t".'</div></li>'."\n";

echo $html;

