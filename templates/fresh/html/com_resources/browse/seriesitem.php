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
$html  = "\t".'<li>'."\n";
$html .= "\t\t".'<a name="resdata_'.$this->line->id.'"></a>'."\n";
$html .= "\t\t".'<p class="';
/*if ($this->line->access == 4) {
	$html .= 'private ';
} elseif ($this->line->access == 3) {
	$html .= 'protected ';
}*/
$html .= 'title"><a href="'.$sef.'">'. Hubzero_View_Helper_Html::xhtml($this->line->title) . '</a>'."\n";
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
	
if ($this->params->get('show_ranking')) {
	$database =& JFactory::getDBO();
	
	// Get statistics info
	$this->helper->getCitationsCount();
	$this->helper->getLastCitationDate();
	
	if ($this->line->type == 7) {
		$stats = new ToolStats($database, $this->line->id, $this->line->type, $this->line->rating, $this->helper->citationsCount, $this->helper->lastCitationDate);
	} else {
		$stats = new AndmoreStats($database, $this->line->id, $this->line->type, $this->line->rating, $this->helper->citationsCount, $this->helper->lastCitationDate);
	}

	$this->line->ranking = round($this->line->ranking, 1);

	$r = (10*$this->line->ranking);
	if (intval($r) < 10) {
		$r = '0'.$r;
	}

	$html .= "\t\t".'<div class="metadata">'."\n";
	$html .= "\t\t\t".'<dl class="rankinfo">'."\n";
	$html .= "\t\t\t\t".'<dt class="ranking"><span class="rank-'.$r.'">'.JText::_('COM_RESOURCES_THIS_HAS').'</span> '.number_format($this->line->ranking,1).' '.JText::_('COM_RESOURCES_RANKING').'</dt>'."\n";
	$html .= "\t\t\t\t".'<dd>'."\n";
	$html .= "\t\t\t\t\t".'<p>'.JText::_('COM_RESOURCES_RANKING_EXPLANATION').'</p>'."\n";
	$html .= "\t\t\t\t\t".'<div>'."\n";
	$html .= $stats->display();
	$html .= "\t\t\t\t\t".'</div>'."\n";
	$html .= "\t\t\t\t".'</dd>'."\n";
	$html .= "\t\t\t".'</dl>'."\n";
	$html .= "\t\t".'</div>'."\n";
} elseif ($this->params->get('show_rating')) {
	switch ($this->line->rating) 
	{
		case 0.5: $class = ' half-stars';      break;
		case 1:   $class = ' one-stars';       break;
		case 1.5: $class = ' onehalf-stars';   break;
		case 2:   $class = ' two-stars';       break;
		case 2.5: $class = ' twohalf-stars';   break;
		case 3:   $class = ' three-stars';     break;
		case 3.5: $class = ' threehalf-stars'; break;
		case 4:   $class = ' four-stars';      break;
		case 4.5: $class = ' fourhalf-stars';  break;
		case 5:   $class = ' five-stars';      break;
		case 0:
		default:  $class = ' no-stars';      break;
	}
	
	$html .= "\t\t".'<div class="metadata">'."\n";
	$html .= "<p></p>";
	$html .= "\t\t\t".'<p class="rating"><span title="'.JText::sprintf('COM_RESOURCES_OUT_OF_5_STARS',$this->line->rating).'" class="avgrating'.$class.'"><span>'.JText::sprintf('COM_RESOURCES_OUT_OF_5_STARS',$this->line->rating).'</span>&nbsp;</span></p>'."\n";
	$html .= "\t\t".'</div>'."\n";
}

$info = array();

//$helper = new ResourcesHelper($this->line->id, $db);
$this->helper->getChildren();
$this->helper->getFirstChild();
$firstchild = $this->helper->firstChild;


if ($this->thedate) {
	$info[] = $this->thedate;
}
if (($this->line->type && $this->params->get('show_type')) || $this->line->standalone == 1) {
	$info[] = $this->line->typetitle;
}
if ($this->helper->contributors && $this->params->get('show_authors')) {
	$info[] = JText::_('COM_RESOURCES_CONTRIBUTORS').': '. $this->helper->contributors;
}

$html .= "\t\t".'<p class="details">'.implode(' <span>|<br/></span> ',$info).'</p>'."\n";

if (!$this->add_anchor){
	if ($this->line->introtext) {
		$html .= "\t\t".Hubzero_View_Helper_Html::shortenText( stripslashes($this->line->introtext) )."\n";
	} else if ($this->line->fulltext) {
		$html .= "\t\t".Hubzero_View_Helper_Html::shortenText( stripslashes($this->line->fulltext) )."\n";
	}
}
else {
	
	$html .='		<a class="get-hidden-resource-description" href="#resdata_'.$this->line->id.'" rel="hidden-info">View Description &rsaquo;</a>'."\n";
	$html .= '		</p>';
}
if ($firstchild != null)
$html .=		 ResourcesHtml::primary_child( $this->option, $this->line, $firstchild);

echo $html;
?>