<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Build the HTML
$html  = '';
$html .= "\t\t" . '<ul class="module-nav"><li><a class="icon-plus" href="' . JRoute::_('index.php?option=com_resources&task=draft') . '">' . JText::_('MOD_MYCONTRIBUTIONS_START_NEW') . '</a></li></ul>'."\n";

$tools = $this->tools;
if ($this->show_tools && $tools)
{
	$html .= '<h4><a href="' . JRoute::_('index.php?option=com_tools&controller=pipeline&task=pipeline') . '">' . JText::_('MOD_MYCONTRIBUTIONS_TOOLS') . ' ';
	if (count($tools) > $this->limit_tools)
	{
		$html .= '<span>' . JText::_('MOD_MYCONTRIBUTIONS_VIEW_ALL') . ' ' . count($tools) . '</span>';
	}
	$html .= '</a></h4>'."\n";

	$html .= '<ul class="compactlist">'."\n";
	for ($i=0; $i < count($tools); $i++)
	{
		if ($i <= $this->limit_tools)
		{
			$class =  $tools[$i]->published ? 'published' : 'draft';
			$urgency = ($this->getState($tools[$i]->state) == 'installed' or $this->getState($tools[$i]->state)=='created') ? ' ' . JText::_('MOD_MYCONTRIBUTIONS_ACTION_REQUIRED') : '' ;

			$html .= '<li class="' . $class . '">' . "\n";
			$html .= '<a href="' . JRoute::_('index.php?option=com_tools&controller=pipeline&task=status&app=' . $tools[$i]->toolname) . '">' . stripslashes($tools[$i]->toolname) . '</a>' . "\n";

			if ($tools[$i]->published)
			{
				$html .= '<span class="extra">'."\n";
				$html .= (!$this->show_wishes) ? '<span class="item_empty ">&nbsp;</span>' : '';
				$html .= (!$this->show_tickets) ? '<span class="item_empty ">&nbsp;</span>' : '';
				if ($this->show_questions)
				{
					$html .= '<span class="item_q">';
					$html .= '<a href="' . JRoute::_('index.php?option=com_answers&task=myquestionsfilterby=open&assigned=1&tag=tool' . $tools[$i]->toolname) . '" title="' . JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_QUESTION' . ($tools[$i]->q > 1 ? 'S' : ''), $tools[$i]->q, $tools[$i]->q_new) . '">' . $tools[$i]->q . '</a>';
					/*if ($tools[$i]->q_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->q_new.'</span>';
					}*/
					$html .= '</span>'."\n";
				}
				else
				{
					$html .= '<span class="item_empty">&nbsp;</span>';
				}
				if ($this->show_wishes)
				{
					$html .= '<span class="item_w">';
					$html .= '<a href="' . JRoute::_('index.php?option=com_wishlist&task=wishlist&category=resource&rid=' . $tools[$i]->rid) . '" title="' . JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_WISH' . ($tools[$i]->w > 1 ? 'S' : ''), $tools[$i]->w, $tools[$i]->w_new) . '">' . $tools[$i]->w . '</a>';
					/*
					if ($tools[$i]->w_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->w_new.'</span>';
					} */
					$html .='</span>'."\n";
				}
				if ($this->show_tickets)
				{
					$html .= '<span class="item_s">';
					$html .= '<a href="' . JRoute::_('index.php?option=com_support&task=tickets&find=group:' . $tools[$i]->devgroup) . '" title="' . JText::sprintf('MOD_MYCONTRIBUTIONS_NUM_TICKET' . ($tools[$i]->s > 1 ? 'S' : ''), $tools[$i]->s, $tools[$i]->s_new) . '">' . $tools[$i]->s . '</a>';
					//$html .= '</span>' . "\n";
					/*
					if ($tools[$i]->s_new > 0) {
						$html .='<br /><span class="item_new">+ '.$tools[$i]->s_new.'</span>';
					} */
					$html .= '</span>'."\n";
				}
			}
			$html .= '<span class="under">' . JText::_('MOD_MYCONTRIBUTIONS_STATUS') . ': <span class="status_' . $this->getState($tools[$i]->state) . '"><a href="' . JRoute::_('index.php?option=com_tools&controller=pipeline&task=status&app=' . $tools[$i]->toolname) . '" title="' . JText::sprintf('MOD_MYCONTRIBUTIONS_TOOL_STATUS', $this->getState($tools[$i]->state), $urgency) . '">' . $this->getState($tools[$i]->state) . '</a></span></span>'."\n";
			$html .= '</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";

	$html .= '<h4><a href="' . JRoute::_('index.php?option=com_members&id=' . $juser->get('id')) . '&active=contributions">' . JText::_('MOD_MYCONTRIBUTIONS_OTHERS_IN_PROGRESS');
	if ($this->contributions && count($this->contributions) > $this->limit_other)
	{
		$html .= '<span>' . JText::_('MOD_MYCONTRIBUTIONS_VIEW_ALL') . '</span>'."\n";
	}
	$html .= '</a></h4>'."\n";
}

$contributions = $this->contributions;
if (!$contributions)
{
	$html .= '<p>' . JText::_('MOD_MYCONTRIBUTIONS_NONE_FOUND') . '</p>'."\n";
}
else
{
	$html .= '<ul class="compactlist">'."\n";
	for ($i=0; $i < count($contributions); $i++)
	{
		if ($i < $this->limit_other)
		{
			// Determine css class
			switch ($contributions[$i]->published)
			{
				case 1:  $class = 'published';  break;  // published
				case 2:  $class = 'draft';      break;  // draft
				case 3:  $class = 'pending';    break;  // pending
			}

			// Get author login
			$author_login = JText::_('MOD_MYCONTRIBUTIONS_UNKNOWN');
			$author = \Hubzero\User\Profile::getInstance($contributions[$i]->created_by);
			if (is_object($author))
			{
				$author_login = stripslashes($author->get('name'));
				if ($author->get('public'))
				{
					$author_login = '<a href="' . JRoute::_($author->getLink()) . '">' . $author_login . '</a>';
				}
			}

			$html .= "\t".'<li class="'.$class.'">'."\n";
			$html .= "\t\t".'<a href="' . JRoute::_('index.php?option=com_resources&task=draft&step=1&id=' . $contributions[$i]->id) . '">' . \Hubzero\Utility\String::truncate(stripslashes($contributions[$i]->title), 40) . '</a>' . "\n";
			$html .= "\t\t".'<span class="under">' . JText::_('MOD_MYCONTRIBUTIONS_TYPE') . ': ' . $contributions[$i]->typetitle . '<br />' . JText::sprintf('MOD_MYCONTRIBUTIONS_SUBMITTED_BY', $author_login) . '</span>' . "\n";
			$html .= "\t".'</li>'."\n";
		}
	}
	$html .= '</ul>'."\n";
}

// Output final HTML
echo $html;
