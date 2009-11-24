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

ximport('Hubzero_View_Helper_Html');

// Questions I asked
$html  = '<h4>'.JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
if ($modmyquestions->openquestions) {
	$openquestions = $modmyquestions->openquestions;
	
	$html .= '<ol class="expandedlist">'.n;			
	for ($i=0; $i < count($openquestions); $i++) 
	{
		if ($i < $modmyquestions->limit_mine) {
			$rcount = (isset($openquestions[$i]->rcount)) ?  $openquestions[$i]->rcount : 0;
			$rclass = ($rcount > 0) ?  'yes' : 'no';
			
			$html .= t.'<li class="question">'.n;				
			$html .= t.t.'<span class="q"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$openquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($openquestions[$i]->subject), 60, 0).'</a></span>'."\n";
			$html .= t.t.'<span class="extra">'.$rcount.'<span class="responses_'.$rclass.'">&nbsp;</span></span>'.n;
			if ($rcount > 0 && $modmyquestions->banking) {
				$html .= t.t.'<p class="earnpoints">'.JText::_('MOD_MYQUESTIONS_CLOSE_THIS_QUESTION').' '.$openquestions[$i]->maxaward.' '.JText::_('MOD_MYQUESTIONS_POINTS').'</p>';
			}
			$html .= t.'</li>'."\n";
		}
	}
	$html .= '</ol>'.n;
} else {
	$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS') .'</p>';
}
$html .= "\t".'<ul class="module-nav">'."\n";
$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_answers&task=new').'">'.JText::_('MOD_MYQUESTIONS_ADD_QUESTION').'</a></li>'."\n";
$html .= "\t".'</ul>'."\n";

// Questions related to my contributions
if ($modmyquestions->show_assigned) {
	$assigned = $modmyquestions->assigned;
	
	$html .= '<h4>'.JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS_ON_CONTRIBUTIONS').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open&assigned=1">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
	if ($assigned) {
		$html .= '<p class="incentive"><span>'.strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')).'</span></p>'."\n";
		$html .= '<ol class="expandedlist">'."\n";			
		for ($i=0; $i < count($assigned); $i++) 
		{
			if ($i < $modmyquestions->limit_assigned) {
				$html .= t.'<li class="question">'."\n";				
				$html .= t.t.'<span class="q"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$assigned[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($assigned[$i]->subject), 60, 0).'</a></span>'."\n";
				if ($modmyquestions->banking) {
					$html .= t.t.'<span class="extra economy">'.$assigned[$i]->maxaward.' <span class="pts">'.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}			
				$html .= t.'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
	} else {
		$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS').'</p>'."\n";
	}
}

// Questions of interest
if ($modmyquestions->show_interests) {
	$juser =& JFactory::getUser();
	$otherquestions = $modmyquestions->otherquestions;
	
	$html .= '<h4>'.JText::_('MOD_MYQUESTIONS_QUESTIONS_TO_ANSWER').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open&interest=1">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
	$html .= t.'<p class="category-header-details">'."\n";
	if ($modmyquestions->interests) {
		$html .= t.t.'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_EDIT').'</a>]</span>'."\n";
	} else {
		$html .= t.t.'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_ADD_INTERESTS').'</a>]</span>'."\n";
	}
	$html .= t.t.'<span class="q">'.JText::_('MOD_MYQUESTIONS_MY_INTERESTS').': '.$modmyquestions->intext.'</span>'."\n";
	$html .= t.'</p>'."\n";
	if ($otherquestions) {
		$html .= '<p class="incentive"><span>'.strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')).'</span></p>'."\n";
		$html .= '<ol class="expandedlist">'."\n";			
		for ($i=0; $i < count($otherquestions); $i++) 
		{
			if ($i < $modmyquestions->limit_interest) {
				$html .= t.'<li class="question">'."\n";				
				$html .= t.t.'<span class="q"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$otherquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($otherquestions[$i]->subject), 60, 0).'</a></span>'."\n";
				if ($modmyquestions->banking) {
					$html .= t.t.'<span class="extra economy">'.$otherquestions[$i]->maxaward.' <span class="pts">'.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}			
				$html .= t.'</li>'."\n";
			}
		}
		$html .= '</ol>'."\n";
	} else {
		$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS') .'</p>'."\n";
	}
	$html .= "\t".'<ul class="module-nav">'."\n";
	$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_answers&task=search').'?filterby=open">'. JText::_('MOD_MYQUESTIONS_ALL_OPEN_QUESTIONS') .'</a></li>'."\n";
	$html .= "\t".'</ul>'."\n";
}

// Output the HTML
echo $html;
?>