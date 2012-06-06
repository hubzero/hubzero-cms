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
		echo "\t\t" . '<description><![CDATA[' . htmlspecialchars(trim(Hubzero_View_Helper_Html::purifyText($tagobj->description))) . ']]></description>' . "\n";
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
			$html .= "\t\t\t\t\t".'<title>'.htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->title)).'</title>'."\n";
			if (isset($row->text) && $row->text != '') {
				$row->text = strip_tags($row->text);
				$html .= "\t\t\t\t\t".'<description><![CDATA['.htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->text)).']]></description>'."\n";
			} else if (isset($row->itext) && $row->itext != '') {
				$row->itext = strip_tags($row->itext);
				$html .= "\t\t\t\t\t".'<description><![CDATA['.htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->itext)).']]></description>'."\n";
			} else if (isset($row->ftext) && $row->ftext != '') {
				$row->ftext = strip_tags($row->ftext);
				$html .= "\t\t\t\t\t".'<description><![CDATA['.htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->ftext)).']]></description>'."\n";
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
