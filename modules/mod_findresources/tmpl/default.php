<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

		$tags = $modfindresources->tags;
		$categories = $modfindresources->categories;
		
		// Start output
		$html = '';
			
		// search
			$html  .= '<form action="/xsearch/" method="get" class="search">'."\n";
			$html  .= ' <fieldset>'."\n";
			$html  .= '  <p>'."\n";
			$html  .= '   <input type="text" name="searchword" value="" />'."\n";
			$html  .= '   <input type="hidden" name="category" value="resources" />'."\n";
			$html  .= '   <input type="submit" value="Search" />'."\n";
			$html  .= '  </p>'."\n";
			$html  .= ' </fieldset>'."\n";
			$html  .= '</form>'."\n";
			
			$tl = array();
			if (count($tags) > 0) {
				$html  .= '<ol class="tags">'."\n";
				$html  .= '    <li>'.JText::_('Popular Tags:').'</li>'."\n";
				foreach ($tags as $tag)
				{
					$tl[$tag->tag] = "\t".'<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
				}
				$html .= implode('',$tl);	
				$html .= '<li><a href="/tags/" class="showmore">'.JText::_('More').' &rsaquo;</a></li>'."\n";
				$html .= '</ol>'."\n";
			} else {
				$html  .= '<p>'.JText::_('No tags found.').'</p>'."\n";
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
					$html  .= '<a href="'.JRoute::_('index.php?option=com_resources'.'&type='.$normalized).'">'.stripslashes($category->type).'</a>';
					$html  .= $i == count($categories) ? '...' : ', ';
					$html  .= "\n";
				}
				$html  .= '<a href="/resources" class="showmore">All Categores &rsaquo;</a>';
				$html  .= '</p>'."\n";
			}
			
			$html  .= '<div class="uploadcontent">'."\n";
			$html  .= ' <h4>Upload your own content! <span><a href="/contribute" class="contributelink">Get started &rsaquo;</a></span></h4>'."\n";
			$html  .= '</div>'."\n";
			
			echo $html;	
?>