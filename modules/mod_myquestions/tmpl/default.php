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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_View_Helper_Html');

// Questions I asked
$html  = '<h4>'.JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=search&area=mine&filterby=open').'">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
if ($this->openquestions) 
{
	$html .= '<ul class="compactlist">'."\n";
	for ($i=0; $i < count($this->openquestions); $i++)
	{
		if ($i < $this->limit_mine) 
		{
			$rcount = (isset($this->openquestions[$i]->rcount)) ?  $this->openquestions[$i]->rcount : 0;
			$rclass = ($rcount > 0) ?  'yes' : 'no';

			$html .= "\t".'<li class="question">'."\n";
			$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$this->openquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($this->openquestions[$i]->subject), 60, 0).'</a>'."\n";
			$html .= "\t\t".'<span><span class="responses_'.$rclass.'">'.$rcount.'</span></span>'."\n";

			if ($rcount > 0 && $this->banking) 
			{
				$html .= "\t\t".'<p class="earnpoints">'.JText::_('MOD_MYQUESTIONS_CLOSE_THIS_QUESTION').' '.$this->openquestions[$i]->maxaward.' '.JText::_('MOD_MYQUESTIONS_POINTS').'</p>';
			}
			$html .= "\t".'</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";
} 
else 
{
	$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS') .'</p>';
}
$html .= "\t".'<ul class="module-nav">'."\n";
$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_answers&task=new').'">'.JText::_('MOD_MYQUESTIONS_ADD_QUESTION').'</a></li>'."\n";
$html .= "\t".'</ul>'."\n";

// Questions related to my contributions
if ($this->show_assigned) 
{
	$html .= '<h4>'.JText::_('MOD_MYQUESTIONS_OPEN_QUESTIONS_ON_CONTRIBUTIONS').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=search&area=assigned&filterby=open').'">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
	if ($this->assigned) 
	{
		$html .= '<p class="incentive"><span>'.strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')).'</span></p>'."\n";
		$html .= '<ul class="compactlist">'."\n";
		for ($i=0; $i < count($this->assigned); $i++)
		{
			if ($i < $this->limit_assigned) 
			{
				$html .= "\t".'<li class="question">'."\n";
				$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$this->assigned[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($this->assigned[$i]->subject), 60, 0).'</a>'."\n";
				if ($this->banking) 
				{
					$html .= "\t\t".'<span ><span class="pts">'.$this->assigned[$i]->maxaward.' '.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}
				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
	} 
	else 
	{
		$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS').'</p>'."\n";
	}
}

// Questions of interest
if ($this->show_interests) 
{
	$juser =& JFactory::getUser();

	$html .= '<h4>'.JText::_('MOD_MYQUESTIONS_QUESTIONS_TO_ANSWER').' <small><a href="'.JRoute::_('index.php?option=com_answers&task=search&area=interest&filterby=open').'">'.JText::_('MOD_MYQUESTIONS_VIEW_ALL').'</a></small></h4>'."\n";
	$html .= "\t".'<p class="category-header-details">'."\n";
	if ($this->interests) 
	{
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_EDIT').'</a>]</span>'."\n";
	} 
	else 
	{
		$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">'.JText::_('MOD_MYQUESTIONS_ADD_INTERESTS').'</a>]</span>'."\n";
	}
	$html .= "\t\t".'<span class="q">'.JText::_('MOD_MYQUESTIONS_MY_INTERESTS').': '.$this->intext.'</span>'."\n";
	$html .= "\t".'</p>'."\n";
	if ($this->otherquestions) 
	{
		$html .= '<p class="incentive"><span>'.strtolower(JText::_('MOD_MYQUESTIONS_BEST_ANSWER_MAY_EARN')).'</span></p>'."\n";
		$html .= '<ul class="compactlist">'."\n";
		for ($i=0; $i < count($this->otherquestions); $i++)
		{
			if ($i < $this->limit_interest) 
			{
				$html .= "\t".'<li class="question">'."\n";
				$html .= "\t\t".'<a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$this->otherquestions[$i]->id).'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($this->otherquestions[$i]->subject), 60, 0).'</a>'."\n";
				if ($this->banking) 
				{
					$html .= "\t\t".'<span><span class="pts">'.$this->otherquestions[$i]->maxaward.' '.strtolower(JText::_('MOD_MYQUESTIONS_PTS')).'</span></span>'."\n";
				}

				$html .= "\t".'</li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
	} 
	else 
	{
		$html .= '<p>'. JText::_('MOD_MYQUESTIONS_NO_QUESTIONS') .'</p>'."\n";
	}
	$html .= "\t".'<ul class="module-nav">'."\n";
	$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_answers&task=search&filterby=open').'">'. JText::_('MOD_MYQUESTIONS_ALL_OPEN_QUESTIONS') .'</a></li>'."\n";
	$html .= "\t".'</ul>'."\n";
}

// Output the HTML
echo $html;
