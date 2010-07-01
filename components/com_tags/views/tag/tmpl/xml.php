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

$document =& JFactory::getDocument();
$document->setMimeEncoding( 'text/xml' );

// Output XML header.
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

// Output root element.
echo '<root>' . "\n";

if (count($this->tags) == 1) {
	$tagobj = $this->tags[0];

	echo "\t" . '<tag>' . "\n";
	echo "\t\t" . '<raw>' . htmlspecialchars( stripslashes($tagobj->raw_tag) ) . '</raw>' . "\n";
	echo "\t\t" . '<normalized>' . htmlspecialchars( $tagobj->tag ) . '</normalized>' . "\n";
	if ($tagobj->description != '') {
		echo "\t\t" . '<description>' . htmlspecialchars( stripslashes($tagobj->description) ) . '</description>' . "\n";
	}
	echo "\t" . '</tag>' . "\n";
}

// Output the data.
$juri =& JURI::getInstance();
$foundresults = false;
$dopaging = false;
$cats = $this->cats;
$jconfig =& JFactory::getConfig();
$html = "\t".'<categories>'."\n";
$k = 0;
foreach ($this->results as $category)
{
	$amt = count($category);
	
	if ($amt > 0) {
		$foundresults = true;
		
		$name  = $cats[$k]['title'];
		$total = $cats[$k]['total'];
		$divid = $cats[$k]['category'];
		
		// Is this category the active category?
		if (!$this->active || $this->active == $cats[$k]['category']) {
			// It is - get some needed info
			$name  = $cats[$k]['title'];
			$total = $cats[$k]['total'];
			$divid = $cats[$k]['category'];
			
			if ($this->active == $cats[$k]['category']) {
				$dopaging = true;
			}
		} else {
			// It is not - does this category have sub-categories?
			if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
				// It does - loop through them and see if one is the active category
				foreach ($cats[$k]['_sub'] as $sub) 
				{
					if ($this->active == $sub['category']) {
						// Found an active category
						$name  = $sub['title'];
						$total = $sub['total'];
						$divid = $sub['category'];
						
						$dopaging = true;
						break;
					}
				}
			}
		}

		$html .= "\t\t" . '<category>'. "\n";
		$html .= "\t\t\t" . '<type>'. $divid . '</type>'."\n";
		$html .= "\t\t\t" . '<title>' . htmlspecialchars( $name ) . '</title>' . "\n";
		$html .= "\t\t\t" . '<total>' . $total. '</total>' . "\n";
		$html .= "\t\t\t" . '<items>'."\n";			
		foreach ($category as $row) 
		{
			$row->href = str_replace('&amp;', '&', $row->href);
			$row->href = str_replace('&', '&amp;', $row->href);
			
			if (strstr( $row->href, 'index.php' )) {
				$row->href = JRoute::_($row->href);
			}
			if (substr($row->href,0,1) == '/') {
				$row->href = substr($row->href,1,strlen($row->href));
			}
				
			$html .= "\t\t\t\t".'<item>'."\n";
			$html .= "\t\t\t\t\t".'<title>'.Hubzero_View_Helper_Html::purifyText($row->title).'</title>'."\n";
			if (isset($row->text) && $row->text != '') {
				$row->text = strip_tags($row->text);
				$html .= "\t\t\t\t\t".'<description>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText($row->text), 200, 0).'</description>'."\n";
			} else if (isset($row->itext) && $row->itext != '') {
				$row->itext = strip_tags($row->itext);
				$html .= "\t\t\t\t\t".'<description>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText($row->itext), 200, 0).'</description>'."\n";
			} else if (isset($row->ftext) && $row->ftext != '') {
				$row->ftext = strip_tags($row->ftext);
				$html .= "\t\t\t\t\t".'<description>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText($row->ftext), 200, 0).'</description>'."\n";
			}
			$html .= "\t\t\t\t\t".'<link>'.$juri->base().$row->href.'</link>'."\n";
			$html .= "\t\t\t\t".'</item>'."\n";
		}
		$html .= "\t\t\t".'</items>'."\n";
		$html .= "\t\t".'</category>'."\n";
	}
	$k++;
}
$html .= "\t".'</categories>'."\n";
echo $html;

// Terminate root element.
echo '</root>' . "\n";