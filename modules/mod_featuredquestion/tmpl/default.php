<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$html = '';
if ($modfeaturedquestion->error) {
	$html .= '<p class="error">'.JText::_('MOD_FEATUREDQUESTION_MISSING_CLASS').'</p>'."\n";
} else {
	if ($modfeaturedquestion->row) {
		ximport('Hubzero_View_Helper_Html');

		$name = JText::_('MOD_FEATUREDQUESTION_ANONYMOUS');
		if ($modfeaturedquestion->row->anonymous == 0) {
			$juser =& JUser::getInstance( $modfeaturedquestion->row->created_by );
			if (is_object($juser)) {
				$name = $juser->get('name');
			}
		}

		$when = $modfeaturedquestion->timeAgo($modfeaturedquestion->mkt($modfeaturedquestion->row->created));

		// Build the HTML
		$html .= '<div class="'.$modfeaturedquestion->cls.'">'."\n";
		$html .= '<h3>'.JText::_('MOD_FEATUREDQUESTION').'</h3>'."\n";
		if (is_file(JPATH_ROOT.$modfeaturedquestion->thumb)) {
			$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$modfeaturedquestion->row->id).'"><img width="50" height="50" src="'.$modfeaturedquestion->thumb.'" alt="" /></a></p>'."\n";
		}
		$html .= '<p><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$modfeaturedquestion->row->id).'">'.stripslashes($modfeaturedquestion->row->subject).'</a>'."\n";
		if ($modfeaturedquestion->row->question) {
			$html .= ': '.Hubzero_View_Helper_Html::shortenText($modfeaturedquestion->encode_html(strip_tags($modfeaturedquestion->row->question)), $modfeaturedquestion->txt_length, 0)."\n";
		}
		$html .= '<br /><span>'.JText::sprintf('MOD_FEATUREDQUESTION_ASKED_BY', $name).'</span> - <span>'.JText::sprintf('MOD_FEATUREDQUESTION_AGO',$when).'</span> - <span>';
		$html .= ($modfeaturedquestion->row->rcount == 1) ? JText::sprintf('MOD_FEATUREDQUESTION_RESPONSE', $modfeaturedquestion->row->rcount) : JText::sprintf('MOD_FEATUREDQUESTION_RESPONSES', $modfeaturedquestion->row->rcount);
		$html .= '</span></p>'."\n";
		$html .= '</div>'."\n";
	}
}

// Output HTML
echo $html;
?>