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

$tags = $modtoptags->tags;

$tl = array();
if (count($tags) > 0) {
	$html  = '<ol class="tags">'."\n";
	foreach ($tags as $tag)
	{
		$tl[$tag->tag] = "\t".'<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag->tag).'">'.$tag->raw_tag.'</a></li>'."\n";
	}
	if ($modtoptags->sortby == 'alphabeta') {
		ksort($tl);
	}
	$html .= implode('',$tl);
	$html .= '</ol>'."\n";
	if ($modtoptags->morelnk) {
		$html .= '<p class="more"><a href="'.JRoute::_('index.php?option=com_tags').'">'.JText::_('MOD_TOPTAGS_MORE').'</a></p>'."\n";
	}
} else {
	$html  = '<p>'.$modtoptags->message.'</p>'."\n";
}
echo $html;
?>