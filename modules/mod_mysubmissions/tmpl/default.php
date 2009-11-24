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

$juser =& JFactory::getUser();
if ($juser->get('guest')) {
	echo '<p class="warning">'.JText::_('MOD_MYSUBMISSIONS_WARNING').'</p>';
} else {
	$rows = $modmysubmissions->rows;
	$steps = $modmysubmissions->steps;
	
	$html = ''; 
	if (!empty($rows)) {
		$stepchecks = array();
		$laststep = (count($steps) - 1);

		foreach ($rows as $row)
		{
			$html .= '<div class="submission">'."\n";
			$html .= '<h4>'.stripslashes($row->title).' <a href="'.JRoute::_('index.php?option=com_contribute&step=1&id='.$row->id).'">'.JText::_('MOD_MYSUBMISSIONS_EDIT').'</a></h4>'."\n";
			$html .= '<table summary="'.JText::_('MOD_MYSUBMISSIONS_EXPLANATION').'">'."\n";
			$html .= "\t".'<tbody>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th>'.JText::_('MOD_MYSUBMISSIONS_TYPE').'</th>'."\n";
			$html .= "\t\t\t".'<td colspan="2">'.$row->typetitle.'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";

			for ($i=1, $n=count( $steps ); $i < $n; $i++) 
			{
				if ($i != $laststep) {
					$check = 'step_'.$steps[$i].'_check';
					$stepchecks[$steps[$i]] = $modmysubmissions->$check( $row->id );

					if ($stepchecks[$steps[$i]]) {
						$completed = '<span class="yes">'.JText::_('MOD_MYSUBMISSIONS_COMPLETED').'</span>';
					} else {
						$completed = '<span class="no">'.JText::_('MOD_MYSUBMISSIONS_NOT_COMPLETED').'</span>';
					}

					$html .= "\t\t".'<tr>'."\n";
					$html .= "\t\t\t".'<th>'.$steps[$i].'</th>'."\n";
					$html .= "\t\t\t".'<td>'.$completed.'</td>'."\n";
					$html .= "\t\t\t".'<td><a href="'.JRoute::_('index.php?option=com_contribute&step='.$i.'&amp;id='.$row->id).'">'.JText::_('MOD_MYSUBMISSIONS_EDIT').'</a></td>';
					$html .= "\t\t".'</tr>'."\n";
				}
			}
			$html .= '</table>'."\n";
			$html .= '<p class="discrd"><a href="'.JRoute::_('index.php?option=com_contribute&task=discard&id='.$row->id).'">'.JText::_('MOD_MYSUBMISSIONS_DELETE').'</a></p>'."\n";
			$html .= '<p class="review"><a href="'.JRoute::_('index.php?option=com_contribute&step='.$laststep.'&id='.$row->id).'">'.JText::_('MOD_MYSUBMISSIONS_REVIEW_SUBMIT').'</a></p>'."\n";
			$html .= '<div class="clear"></div>';
			$html .= '</div>'."\n";
		}
	} else {
		$html .= '<p>'.JText::_('MOD_MYSUBMISSIONS_NONE').'</p>'.n;
	}

	echo $html;
}
?>