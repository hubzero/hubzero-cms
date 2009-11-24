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
if ($modfeaturedresource->error) {
	$html .= '<p class="error">'.JText::_('MOD_FEATUREDRESOURCE_MISSING_CLASS').'</p>'."\n";
} else {
	ximport('Hubzero_View_Helper_Html');
	
	if ($modfeaturedresource->row) {
		$html .= '<div class="'.$modfeaturedresource->cls.'">'."\n";
		$html .= '<h3>'.JText::_('MOD_FEATUREDRESOURCE_FEATURED').' '.$modfeaturedresource->row->typetitle.'</h3>'."\n";
		if (is_file(JPATH_ROOT.$modfeaturedresource->thumb)) {
			$html .= '<p class="featured-img"><a href="'.JRoute::_('index.php?option=com_resources&id='.$modfeaturedresource->id).'"><img width="50" height="50" src="'.$modfeaturedresource->thumb.'" alt="" /></a></p>'."\n";
		}
		$html .= '<p><a href="'.JRoute::_('index.php?option=com_resources&id='.$modfeaturedresource->id).'">'.stripslashes($modfeaturedresource->row->title).'</a>: '."\n";
		if ($modfeaturedresource->row->introtext) {
			$html .= Hubzero_View_Helper_Html::shortenText($modfeaturedresource->encode_html(strip_tags($modfeaturedresource->row->introtext)), $modfeaturedresource->txt_length, 0)."\n";
		}
		$html .= '</p>'."\n";
		$html .= '</div>'."\n";
	}
}

// Output HTML
echo $html;
?>