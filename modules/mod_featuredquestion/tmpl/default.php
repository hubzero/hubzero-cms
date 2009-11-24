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