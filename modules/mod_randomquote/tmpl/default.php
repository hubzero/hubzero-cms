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

if ($modrandomquote->quote) {
	$html  = '<h3 class="notable_quote">'.JText::_('Notable Quote').'</h3>'."\n";
	$html .= '<div class="frontquote">'."\n";
	$html .= ' <blockquote cite="'.$modrandomquote->quote->fullname.'"><p>'."\n";
	$html .= Hubzero_View_Helper_Html::shortenText(stripslashes($modrandomquote->quote_to_show), $modrandomquote->charlimit, 0)."\n" ;
	$html .= strlen($modrandomquote->quote->quote) > $modrandomquote->charlimit 
	? '<a href="/about/quotes/?quoteid='.$modrandomquote->quote->id.'" title="'.JText::_('View the full quote by').' '.$modrandomquote->quote->fullname.'" class="showfullquote">...&raquo;</a>'."\n" 
	: '' ;
	$html .= ' </p></blockquote>'."\n";
	$html .= '<p class="cite"><cite>'.$modrandomquote->quote->fullname.'</cite>, '.$modrandomquote->quote->org.' <span>-</span> <span>'.JText::_('in').'&nbsp;<a href="/about/quotes">'.JText::_('Notable&nbsp;Quotes').'</a></span></p>'."\n";
	//$html .= ' <p class="cite"><cite>'.$modrandomquote->quote->fullname.'</cite> - '.JText::_('in').'&nbsp;<a href="/about/quotes">'.JText::_('Quotes').'</a></p>'."\n";
	$html .= '</div>'."\n";
	
	echo $html;
}