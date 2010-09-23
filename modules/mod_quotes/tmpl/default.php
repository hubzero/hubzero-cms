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

$quotes = $modquotes->quotes;
$filters = $modquotes->filters;

$html  = '';
$html .= '<div id="content-header-extra">'."\n";
$html .= ' <ul id="useroptions">'."\n";
$html .= ' <li><a href="'.JRoute::_('index.php?option=com_feedback&task=success_story').'" class="add">'.JText::_('Add Your Success Story').'</a></li>'."\n";
$html .= ' </ul>'."\n";
$html .= '</div>'."\n";
// Did we get any results?
if (count($quotes) > 0) {
	// Yes - loop through and build the HTML
	foreach ($quotes as $quote)
	{
		$quote->org = str_replace('<br>','<br />',$quote->org);

		if (isset($filters['id']) && $filters['id'] != '') {
			$html .= '<div class="breadcrumbs"><p><a href="/about/quotes" class="breadcrumbs">'.JText::_('NOTABLE_QUOTES').'</a> &rsaquo; <strong>';
			$html .= stripslashes($quote->fullname).'</strong></p></div>'."\n\n";
		}
		$html .= '<blockquote cite="'.stripslashes($quote->fullname).'">'."\n";
		if (isset($filters['id']) && $filters['id'] != '') {
			$html .= "\t".'<p>'.stripslashes($quote->quote).'</p>'."\n";
		} else {
			$html .= "\t".'<p>'.stripslashes($quote->short_quote)."\n";
			if ($quote->short_quote != $quote->quote) {
				$html .= "\t".' &#8230; <a href="/about/quotes/?quoteid='.$quote->id.'" title="'.JText::sprintf('VIEW_QUOTE_BY',stripslashes($quote->fullname)).'">'.JText::_('MORE').'</a>';
			}
			$html .= '</p>'."\n";
		}
		$html .= '</blockquote>'."\n";
		$html .= '<p class="cite">';
		$html .= '<cite>'.stripslashes($quote->fullname).'</cite>';
		$quote->org = stripslashes($quote->org);
		$quote->org = str_replace('&amp;','&',$quote->org);
		$quote->org = str_replace('&','&amp;',$quote->org);
		$html .= '<br />'.$quote->org.'</p>'."\n\n";

	}
} else {
	// No - show message
	$html = '<p>'.JText::_('NO_QUOTES_FOUND').'</p>'."\n";
}

// Output HTML
echo $html;
?>
