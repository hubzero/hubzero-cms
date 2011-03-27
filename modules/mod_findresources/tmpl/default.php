<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

$tags = $modfindresources->tags;
$categories = $modfindresources->categories;
			
// search
$html  = '<form action="/ysearch/" method="get" class="search">'."\n";
$html .= ' <fieldset>'."\n";
$html .= '  <p>'."\n";
$html .= '   <label for="rsearchword">'.JText::_('Keyword or phrase:').'</label>'."\n";
$html .= '   <input type="text" name="terms" id="rsearchword" value="" />'."\n";
$html .= '   <input type="hidden" name="section" value="resources" />'."\n";
$html .= '   <input type="submit" value="'.JText::_('Search').'" />'."\n";
$html .= '  </p>'."\n";
$html .= ' </fieldset>'."\n";
$html .= '</form>'."\n";

$tl = array();
if (count($tags) > 0) {
	$html .= '<ol class="tags">'."\n";
	$html .= "\t".'<li>'.JText::_('Popular Tags:').'</li>'."\n";
	foreach ($tags as $tag)
	{
		$tl[$tag->tag] = "\t".'<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
	}
	$html .= implode('',$tl);	
	$html .= "\t".'<li><a href="'.JRoute::_('index.php?option=com_tags').'" class="showmore">'.JText::_('More tags').' &rsaquo;</a></li>'."\n";
	$html .= '</ol>'."\n";
} else {
	$html .= '<p>'.JText::_('No tags found.').'</p>'."\n";
}

if (count($categories) > 0) {
	$html  .= '<p>'."\n";
	$i = 0;
	foreach ($categories as $category) 
	{
		$i++;
		$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $category->type);
		$normalized = strtolower($normalized);
		
		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
		$html  .= '<a href="'.JRoute::_('index.php?option=com_resources&type='.$normalized).'">'.stripslashes($category->type).'</a>';
		$html  .= $i == count($categories) ? '...' : ', ';
		$html  .= "\n";
	}
	$html  .= '<a href="'.JRoute::_('index.php?option=com_resources').'" class="showmore">'.JText::_('All Categores &rsaquo;').'</a>';
	$html  .= '</p>'."\n";
}

$html  .= '<div class="uploadcontent">'."\n";
$html  .= "\t".'<h4>'.JText::_('Upload your own content!').' <span><a href="'.JRoute::_('index.php?option=com_contribute').'" class="contributelink">'.JText::_('Get started &rsaquo;').'</a></span></h4>'."\n";
$html  .= '</div>'."\n";

echo $html;

