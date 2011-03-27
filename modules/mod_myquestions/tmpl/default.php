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

ximport('Hubzero_View_Helper_Html');

// Questions I asked
$html  = '<h4>'.JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
if ($modmyquestions->openquestions) {
	$openquestions = $modmyquestions->openquestions;
	
	$html .= '<ul class="compactlist">'."\n";			
	for ($i=0; $i < count($openquestions); $i++) 
	{
		if ($i < $modmyquestions->limit_mine) {
			$rcount = (isset($openquestions[$i]->rcount)) ?  $openquestions[$i]->rcount : 0;
			$rclass = ($rcount > 0) ?  'yes' : 'no';
			
			$html .= "\t".'<li class="question">'."\n";
			$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$openquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($openquestions[$i]->subject), 60, 0).'</a>'."\n";							
			$html .= "\t\t".'<span><span class="responses_'.$rclass.'">'.$rcount.'</span></span>'."\n";
			
			if ($rcount > 0 && $modmyquestions->banking) {
				$html .= "\t\t".'<p class="earnpoints">'.JText::_('MOD_MYQUESTIONS_CLOSE_THIS_QUESTION').' '.$openquestions[$i]->maxaward.' '.JText::_('MOD_MYQUESTIONS_POINTS').'</p>';
			}
			$html .= "\t".'</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";
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
		$html .= '<ul class="compactlist">'."\n";			
		for ($i=0; $i < count($assigned); $i++) 
		{
			if ($i < $modmyquestions->limit_assigned) {
				$html .= "\t".'<li class="question">'."\n";
				$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$assigned[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($assigned[$i]->subject), 60, 0).'</a>'."\n";					
				if ($modmyquestions->banking) {
					$html .= "\t\t".'<span ><span class="pts">'.$assigned[$i]->maxaward.' '.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}						
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
	} else {
		$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS').'</p>'."\n";
	}
}

// Questions of interest
if ($modmyquestions->show_interests) {
	$juser =& JFactory::getUser();
	$otherquestions = $modmyquestions->otherquestions;
	
	$html .= '<h4>'.JText::_('MOD_MYQUESTIONS_QUESTIONS_TO_ANSWER').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open&interest=1">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
	$html .= "\t".'<p class="category-header-details">'."\n";
	if ($modmyquestions->interests) {
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_EDIT').'</a>]</span>'."\n";
	} else {
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_ADD_INTERESTS').'</a>]</span>'."\n";
	}
	$html .= "\t\t".'<span class="q">'.JText::_('MOD_MYQUESTIONS_MY_INTERESTS').': '.$modmyquestions->intext.'</span>'."\n";
	$html .= "\t".'</p>'."\n";
	if ($otherquestions) {
		$html .= '<p class="incentive"><span>'.strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')).'</span></p>'."\n";
		$html .= '<ul class="compactlist">'."\n";			
		for ($i=0; $i < count($otherquestions); $i++) 
		{
			if ($i < $modmyquestions->limit_interest) {
				$html .= "\t".'<li class="question">'."\n";
				$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$otherquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($otherquestions[$i]->subject), 60, 0).'</a>'."\n";
				if ($modmyquestions->banking) {
					$html .= "\t\t".'<span><span class="pts">'.$otherquestions[$i]->maxaward.' '.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}					
						
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
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