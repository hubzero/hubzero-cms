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

$rows = $modrecentquestions->rows;
if (count($rows) > 0) {
	require_once( JPATH_ROOT.DS.'components'.DS.'com_answers'.DS.'helpers'.DS.'tags.php' );
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
				$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id')).'">'.stripslashes($juser->get('name')).'</a>';
			}
		}

		//$when = $modrecentquestions->timeAgo($modrecentquestions->mkt($row->created));

		$tags = $tagging->get_tag_cloud(0, 0, $row->id);

		$html .= "\t\t".' <li>'."\n";
		if ($modrecentquestions->style == 'compact') {
			$html .= "\t\t\t".'<a href="'. JRoute::_('index.php?option=com_answers&task=question&id='.$row->id) .'">'.$row->subject.'</a>'."\n";
			$html .= '<span> - ';
			$html .= ($row->rcount == 1) ? JText::sprintf('MOD_RECENTQUESTIONS_RESPONSE', $row->rcount) : JText::sprintf('MOD_RECENTQUESTIONS_RESPONSES', $row->rcount);
			$html .= '</span>';
		} else {
			$html .= "\t\t\t".'<h4><a href="'. JRoute::_('index.php?option=com_answers&task=question&id='.$row->id) .'" title="'.htmlentities(stripslashes($row->subject),ENT_COMPAT,'UTF-8').'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($row->subject),100,0).'</a></h4>'."\n";
			/*if ($row->question) {
				$html .= "\t\t\t".'<p class="snippet">';
				$html .= Hubzero_View_Helper_Html::shortenText($row->question, 100, 0);
				$html .= '</p>'."\n";
			}*/
			$html .= '<p class="entry-details">'."\n";
			$html .= '	'. JText::sprintf('MOD_RECENTQUESTIONS_ASKED_BY', $name) .' @ '."\n";
			$html .= '	<span class="entry-time">'. JHTML::_('date',$row->created, '%I:%M %p', 0) .'</span> on '."\n";
			$html .= '	<span class="entry-date">'. JHTML::_('date',$row->created, '%d %b %Y', 0) .'</span>'."\n";
			$html .= '	<span class="entry-details-divider">&bull;</span>'."\n";
			$html .= '	<span class="entry-comments">'."\n";
			$html .= '		<a href="'. JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'#answers') .'" title="'. JText::sprintf('MOD_RECENTQUESTIONS_RESPONSES', $row->rcount) .'">'."\n";
			$html .= '			'.$row->rcount."\n";
			$html .= '		</a>'."\n";
			$html .= '	</span>'."\n";
			$html .= '</p>'."\n";
			$html .= "\t\t\t".'<p class="entry-tags">'.JText::_('MOD_RECENTQUESTIONS_TAGS').':</p> '.$tags."\n";
		}
		$html .= "\t\t".' </li>'."\n";
	}
	$html .= "\t\t".'</ul>'."\n";
} else {
	$html  = "\t\t".'<p>'.JText::_('MOD_RECENTQUESTIONS_NO_RESULTS').'</p>'."\n";
}

echo $html;
?>