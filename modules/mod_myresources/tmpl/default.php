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

$juser =& JFactory::getUser();

$html = '';
if (!$no_html) {
	$slf = ($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : JRoute::_('index.php?option=com_members&task=myaccount&active=dashboard');
	$html .= '<form method="get" action="'. $slf .'" id="myresources-form" enctype="multipart/form-data">';
	$html .= '<h4>'.JText::_('Sort by');
	$html .= ' <select name="params[sort]" id="myresources-sort">'."\n";
	$html .= '<option value="publish_up"';
	if ($modmyresources->sort == 'publish_up') {
		$html .= ' selected="selected"';
	}
	$html .= '>Date</option>'."\n";
	$html .= '<option value="title"';
	if ($modmyresources->sort == 'title') {
		$html .= ' selected="selected"';
	}
	$html .= '>Title</option>'."\n";
	$html .= '<option value="usage"';
	if ($modmyresources->sort == 'usage') {
		$html .= ' selected="selected"';
	}
	$html .= '>Usage</option>'."\n";
	$html .= '</select>'."\n";
	$html .= ' &nbsp; '.JText::_('Show')."\n";
	$html .= ' <select name="params[limit]" id="myresources-limit">'."\n";
	$html .= '<option value="5"';
	if ($modmyresources->limit == 5) {
		$html .= ' selected="selected"';
	}
	$html .= '>5</option>'."\n";
	$html .= '<option value="10"';
	if ($modmyresources->limit == 10) {
		$html .= ' selected="selected"';
	}
	$html .= '>10</option>'."\n";
	$html .= '<option value="20"';
	if ($modmyresources->limit == 20) {
		$html .= ' selected="selected"';
	}
	$html .= '>20</option>'."\n";
	$html .= '<option value="50"';
	if ($modmyresources->limit == 50) {
		$html .= ' selected="selected"';
	}
	$html .= '>50</option>'."\n";
	$html .= '<option value="100"';
	if ($modmyresources->limit == 100) {
		$html .= ' selected="selected"';
	}
	$html .= '>100</option>'."\n";
	$html .= '<option value="all"';
	if ($modmyresources->limit == 0) {
		$html .= ' selected="selected"';
	}
	$html .= '>All</option>'."\n";
	$html .= '</select>'."\n";
	$html .= '</h4>'."\n";
}
$html .= '<div id="myresources-content">';
$contributions = $modmyresources->contributions;
if (!$contributions) {
	$html .= '<p>'.JText::_('MOD_MYRESOURCES_NONE_FOUND').'</p>'."\n";
} else {
	ximport('Hubzero_View_Helper_Html');
	//$config =& JComponentHelper::getParams( 'com_resources' );

	$html .= '<ul class="expandedlist">'."\n";
	for ($i=0; $i < count($contributions); $i++)
	{
		// Determine css class
		switch ($contributions[$i]->published)
		{
			case 1:  $class = 'published';  break;  // published
			case 2:  $class = 'draft';      break;  // draft
			case 3:  $class = 'pending';    break;  // pending
			case 0:  $class = 'deleted';    break;  // pending
		}

		/*$rparams = new JParameter( $contributions[$i]->params );
		$params = $config;
		$params->merge( $rparams );

		// Set the display date
		switch ($params->get('show_date')) 
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $contributions[$i]->created, '%d %b %Y');    break;
			case 2: $thedate = JHTML::_('date', $contributions[$i]->modified, '%d %b %Y');   break;
			case 3: $thedate = JHTML::_('date', $contributions[$i]->publish_up, '%d %b %Y'); break;
		}*/
		$thedate = JHTML::_('date', $contributions[$i]->publish_up, '%d %b %Y');

		$html .= "\t".'<li class="'.$class.'">'."\n";
		$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_resources&id='.$contributions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($contributions[$i]->title), 40, 0).'</a>'."\n";
		$html .= "\t\t".'<span class="under">'.$thedate.' &nbsp; '.stripslashes($contributions[$i]->typetitle).'</span>'."\n";
		$html .= "\t".'</li>'."\n";
	}
	$html .= '</ul>'."\n";
	/*if ($no_html) {
		$html .= '<p>Sort: '.$modmyresources->sort.' Limit:'.$modmyresources->limit.'</p>';
	}*/
}
$html .= '</div>';
if (!$no_html) {
	$html .= "\t\t".'<ul class="module-nav"><li><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=contributions&area=resources').'">'.JText::_('MOD_MYRESOURCES_ALL_PUBLICATIONS').'</a></li></ul>'."\n";
	$html .= '</form>';
}
// Output final HTML
echo $html;
?>
