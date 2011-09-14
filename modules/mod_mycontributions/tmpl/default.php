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

// Build the HTML
$html = '';
$tools = $modmycontributions->tools;
if ($modmycontributions->show_tools && $tools) {
	$html .= '<h4>'.JText::_('MOD_MYCONTRIBUTIONS_TOOLS').' ';
	if (count($tools) > $modmycontributions->limit_tools)  {
		$html .= '<small><a href="'.JRoute::_('index.php?option=com_contribtool').'">'.JText::_('MOD_MYCONTRIBUTIONS_VIEW_ALL').' '.count($tools).'</a></small>';
	}
	$html .= '</h4>'."\n";
	//$html .= '<div class="category-wrap">'.n;
	$html .= '<ul class="compactlist">'."\n";
	for ($i=0; $i < count($tools); $i++)
	{
		if ($i <= $modmycontributions->limit_tools) {
			$class =  $tools[$i]->published ? 'published' : 'draft';
			$urgency = ($modmycontributions->getState($tools[$i]->state) == 'installed' or $modmycontributions->getState($tools[$i]->state)=='created') ? ' '.JText::_('and requires your action') : '' ;

			$html .= t.'<li class="'.$class.'">'."\n";
			$html .= t.t.'<a href="'.JRoute::_('index.php?option=com_contribtool&task=status&toolid='.$tools[$i]->id).'">'.stripslashes($tools[$i]->toolname).'</a>'.n;

			if ($tools[$i]->published) {
				$html .= t.t.'<span class="extra">'."\n";
				$html .= (!$modmycontributions->show_wishes) ? '<span class="item_empty ">&nbsp;</span>' : '';
				$html .= (!$modmycontributions->show_tickets) ? '<span class="item_empty ">&nbsp;</span>' : '';
				if ($modmycontributions->show_questions) {
					$html .= t.t.t.'<span class="item_q"><a href="'.JRoute::_('index.php?option=com_answers&task=myquestions').'?filterby=open&amp;assigned=1&amp;tag=tool'.$tools[$i]->toolname.'" ';
					$html .= ' title="';
					if ($tools[$i]->q == 1) {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_QUESTION', $tools[$i]->q, $tools[$i]->q_new);
					} else {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_QUESTIONS', $tools[$i]->q, $tools[$i]->q_new);
					}
					$html .= '">'.$tools[$i]->q.'</a>';
					/*if ($tools[$i]->q_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->q_new.'</span>';
					}*/
					$html .= '</span>'."\n";
				} else {
					$html .= t.t.t.'<span class="item_empty">&nbsp;</span>';
				}
				if ($modmycontributions->show_wishes) {
					$html .= t.t.t.'<span class="item_w"><a href="'.JRoute::_('index.php?option=com_wishlist&task=wishlist&category=resource&rid='.$tools[$i]->rid).'"';
					$html .= ' title="';
					if ($tools[$i]->w == 1) {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_WISH', $tools[$i]->w, $tools[$i]->w_new);
					} else {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_WISHES', $tools[$i]->w, $tools[$i]->w_new);
					}
					$html .= '">'.$tools[$i]->w.'</a>';
					/*
					if ($tools[$i]->w_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->w_new.'</span>';
					} */
					$html .='</span>'."\n";
				}
				if ($modmycontributions->show_tickets) {
					$html .= t.t.t.'<span class="item_s"><a href="'.JRoute::_('index.php?option=com_support&task=tickets').'?find=group:'.$tools[$i]->devgroup.'"';
					$html .= ' title="';
					if ($tools[$i]->s == 1) {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_TICKET', $tools[$i]->s, $tools[$i]->s_new);
					} else {
						$html .= JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_TICKETS', $tools[$i]->s, $tools[$i]->s_new);
					}
					$html .= '">'.$tools[$i]->s.'</a></span>'."\n";
					/*
					if ($tools[$i]->s_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->s_new.'</span>';
					} */
					$html .= t.t.'</span>'."\n";
				}
			}
			$html .= t.t.'<span class="under">'.JText::_('Status').': <span class="status_'.$modmycontributions->getState($tools[$i]->state).'"><a href="'.JRoute::_('index.php?option=com_contribtool&task=status&toolid='.$tools[$i]->id).'" title="'.JText::_('This tool is now in').' '.$modmycontributions->getState($tools[$i]->state).' '.JText::_('status').$urgency.'">'.$modmycontributions->getState($tools[$i]->state).'</a></span></span>'."\n";
			$html .= t.'</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";
	//$html .= '</div>'.n;
	$html .= '<h4>'.JText::_('MOD_MYCONTRIBUTIONS_OTHERS_IN_PROGRESS');
	if ($modmycontributions->contributions && count($modmycontributions->contributions) > $modmycontributions->limit_other)  {
		$juser =& JFactory::getUser();
		$html .= ' <small><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id')).DS.'contributions">'.JText::_('MOD_MYCONTRIBUTIONS_VIEW_ALL').'</a></small>'.n;
	}
	$html .= '</h4>'."\n";
}

$contributions = $modmycontributions->contributions;
if (!$contributions) {
	$html .= '<p>'.JText::_('MOD_MYCONTRIBUTIONS_NONE_FOUND').'</p>'."\n";
} else {
	ximport('Hubzero_View_Helper_Html');

	$html .= '<ul class="compactlist">'."\n";
	for ($i=0; $i < count($contributions); $i++)
	{
		if ($i < $modmycontributions->limit_other) {
			// Determine css class
			switch ($contributions[$i]->published)
			{
				case 1:  $class = 'published';  break;  // published
				case 2:  $class = 'draft';      break;  // draft
				case 3:  $class = 'pending';    break;  // pending
			}

			// get author login
			$author_login = JText::_('MOD_MYCONTRIBUTIONS_UNKNOWN');
			$author =& JUser::getInstance( $contributions[$i]->created_by );
			if (is_object($author)) {
				$author_login = '<a href="'.JRoute::_('index.php?option=com_members&id='.$author->get('id')).'">'.stripslashes($author->get('name')).'</a>';
			}
			$href = '/contribute/?step=1&amp;id='.$contributions[$i]->id;

			$html .= "\t".'<li class="'.$class.'">'."\n";
			$html .= "\t\t".'<a href="'.$href.'">'.Hubzero_View_Helper_Html::shortenText(stripslashes($contributions[$i]->title), 40, 0).'</a>'."\n";
			$html .= "\t\t".'<span class="under">'.JText::_('MOD_MYCONTRIBUTIONS_TYPE').': '.$modmycontributions->getType($contributions[$i]->type).'<br />'.JText::sprintf('MOD_MYCONTRIBUTIONS_SUBMITTED_BY',$author_login).'</span>'."\n";
			$html .= "\t".'</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";
}

$html .= "\t\t".'<ul class="module-nav"><li><a href="/contribute/?task=start">'.JText::_('MOD_MYCONTRIBUTIONS_START_NEW').'</a></li></ul>'."\n";

// Output final HTML
echo $html;
?>