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

if ($this->line->alias) {
	$sef = JRoute::_('index.php?option='.$this->option.'&alias='. $this->line->alias);
} else {
	$sef = JRoute::_('index.php?option='.$this->option.'&id='. $this->line->id);
}
$html  = "\t".'<li id="thumb-list">'."\n";
$html .= "\t\t".'<p class="';
/*if ($this->line->access == 4) {
	$html .= 'private ';
} elseif ($this->line->access == 3) {
	$html .= 'protected ';
}*/
$class =  ($this->minimalist)? '' : 'class="highlight-title"';
$html .= 'title"><a '.$class.' href="'.$sef.'">'. Hubzero_View_Helper_Html::xhtml($this->line->title) . '</a>'."\n";
if ($this->show_edit != 0) {
	if ($this->line->published >= 0) {
		if ($this->line->type == 7) {
			$link = JRoute::_('index.php?option=com_contribtool&task=start&step=1&rid='. $this->line->id);
		} else {
			$link = JRoute::_('index.php?option=com_contribute&step=1&id='. $this->line->id);
		}
		$html .= ' <a class="edit button" href="'. $link .'" title="'. JText::_('COM_RESOURCES_EDIT') .'">'. JText::_('COM_RESOURCES_EDIT') .'</a>';
	}
}
$html .= '</p>'."\n";

$info = array();
if ($this->thedate) {
	$info[] = $this->thedate;
}
if (!$this->minimalist) {
	//$html .= '<div class="aside">'."\n";
	$real_path = '/www/neeshub/';
	$thumb_path = 'site/images/highlights/';
	$thumb_path .= $this->line->id;
	$thumb_path .= '.gif';
	if (!(file_exists($real_path.$thumb_path))) {
		$thumb_path = '/site/images/highlights/thumbdefault.gif'; }
		
	$html .='<a class="highlight-title" href="'.$sef.'">';
	$html .= '<img width="175" height="90" class="resource-thumb-image" src="'.$thumb_path.'" align="left"></img>';
	$html .= '</a>'."\n";
}
//$html .= '</div><!-- /aside -->'."\n";
//$html .= '<div class="subject">'."\n";
$html .= "\t\t".'<p class="details">'.implode(' <span>|<br/></span> ',$info).'</p>'."\n";
$text = '';
if ($this->line->introtext) {
	$text .= "\t\t".Hubzero_View_Helper_Html::shortenText( stripslashes($this->line->introtext) )."\n";
} else if ($this->line->fulltext) {
	$text .= "\t\t".Hubzero_View_Helper_Html::shortenText( stripslashes($this->line->fulltext) )."\n";
}
$stringlength = ($this->minimalist)? 450 : 83;
if (strlen($text) > $stringlength)
$text = substr($text, 0, $stringlength) . ' <strong>...</strong>';

$html .= $text;
$this->helper->GetFirstChild();
//$html .= '</div><!-- /subject -->'."\n";

//$html .= ResourcesHtml::primary_child( $this->option, $this->line, $this->helper->firstChild);

/*
$html .='		<a class="get-hidden-resource-description" href="#resdata_'.$this->line->id.'" rel="hidden-info">View Description</a>'."\n";$html .= '		</p>';



$html .='		<div class="hidden-resource-info">'."\n";
$html .='		<div id="resdata_'.$this->line->id.'">'."\n";

$html .=		 ResourcesHtml::primary_child( $this->option, $this->line, $this->helper->firstChild);


$html .='		<p>'.stripslashes($this->helper->firstChild->title).'</p>'."\n";
$html .='		</div>'."\n";
$html .='		</div>'."\n";
$html .= '<script>'."\n";
$html .= '	$jQ("a.get-hidden-resource-description").fancybox( '."\n";
$html .= '			{'."\n";
$html .= '				\'hideOnContentClick\': false,'."\n";
$html .= '				\'transitionIn\'	:	\'elastic\','."\n";
$html .= '				\'transitionOut\'	:	\'elastic\','."\n";
$html .= '				\'overlayShow\' : true,'."\n";
$html .= '				\'autoDimensions\' : false,'."\n";
$html .= '				\'width\' : 300,'."\n";
$html .= '				\'height\' : 300,'."\n";
$html .= '			}'."\n";
$html .= '	); '."\n";
$html .= '	</script>'."\n";
*/

echo $html;
?>