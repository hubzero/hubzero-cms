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

$rows = $modrecentquestions->rows;
if (count($rows) > 0) {
	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'answers.tags.php' );
	$database =& JFactory::getDBO();
	$tagging = new AnswersTags( $database );
	
	ximport('Hubzero_View_Helper_Html');
	
	$html  = "\t\t".'<ul class="questions">'."\n";
	foreach ($rows as $row) 
	{
		$name = JText::_('MOD_RECENTQUESTIONS_ANONYMOUS');
		if ($row->anonymous == 0) {
			$juser =& JUser::getInstance( $row->created_by );
			if (is_object($juser)) {
				$name = $juser->get('name');
			}
		}

		$when = $modrecentquestions->timeAgo($modrecentquestions->mkt($row->created));
		
		$tags = $tagging->get_tag_cloud(0, 0, $row->id);
		
		$html .= "\t\t".' <li>'."\n";
		if ($modrecentquestions->style == 'compact') {
			$html .= "\t\t\t".'<a href="'. JRoute::_('index.php?option=com_answers&task=question&id='.$row->id) .'">'.$row->subject.'</a>'."\n";
			$html .= '<span> - ';
			$html .= ($row->rcount == 1) ? JText::sprintf('MOD_RECENTQUESTIONS_RESPONSE', $row->rcount) : JText::sprintf('MOD_RECENTQUESTIONS_RESPONSES', $row->rcount);
			$html .= '</span>';
		} else {
			$html .= "\t\t\t".'<h4><a href="'. JRoute::_('index.php?option=com_answers&task=question&id='.$row->id) .'">'.$row->subject.'</a></h4>'."\n";
			$html .= "\t\t\t".'<p class="snippet">';
			if ($row->question) {
				$html .= Hubzero_View_Helper_Html::shortenText($row->question, 100, 0);
			}
			$html .= '</p>'."\n";
			$html .= "\t\t\t".'<p>'.JText::sprintf('MOD_RECENTQUESTIONS_ASKED_BY', $name).' - '.$when.' '.JText::_('MOD_RECENTQUESTIONS_AGO').' - ';
			$html .= ($row->rcount == 1) ? JText::sprintf('MOD_RECENTQUESTIONS_RESPONSE', $row->rcount) : JText::sprintf('MOD_RECENTQUESTIONS_RESPONSES', $row->rcount);
			$html .= '</p>'."\n";
			$html .= "\t\t\t".'<p>'.JText::_('MOD_RECENTQUESTIONS_TAGS').':</p> '.$tags."\n";
		}
		$html .= "\t\t".' </li>'."\n";
	}
	$html .= "\t\t".'</ul>'."\n";
} else {
	$html  = "\t\t".'<p>'.JText::_('MOD_RECENTQUESTIONS_NO_RESULTS').'</p>'."\n";
}

echo $html;
?>