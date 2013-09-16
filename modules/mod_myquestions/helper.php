<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Module');

/**
 * Module class for displaying a user's questions
 * Requires com_answers component
 */
class modMyQuestions extends Hubzero_Module
{
	/**
	 * Format the tags
	 * 
	 * @param      string  $string String of comma-separated tags
	 * @param      number  $num    Number of tags to display
	 * @param      integer $max    Max character length
	 * @return     string HTML
	 */
	private function _formatTags($string='', $num=3, $max=25)
	{
		$out = '';
		$tags = explode(',', $string);

		if (count($tags) > 0) 
		{
			$out .= '<span class="taggi">' . "\n";
			$counter = 0;

			for ($i=0; $i< count($tags); $i++)
			{
				$counter = $counter + strlen(stripslashes($tags[$i]));
				if ($counter > $max) 
				{
					$num = $num - 1;
				}
				if ($i < $num) 
				{
					// display tag
					$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $tags[$i]);
					$normalized = strtolower($normalized);
					$out .= "\t" . '<a href="'.JRoute::_('index.php?option=com_tags&tag=' . $normalized) . '">' . stripslashes($tags[$i]) . '</a> ' . "\n";
				}
			}
			if ($i > $num) 
			{
				$out .= ' (&#8230;)';
			}
			$out .= '</span>' . "\n";
		}

		return $out;
	}

	/**
	 * Looks up a user's interests (tags)
	 * 
	 * @param      integer $cloud Output as tagcloud (defaults to no)
	 * @return     string  List of tags as either a tagcloud or comma-delimitated string 
	 */
	private function _getInterests($cloud=0)
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'tags.php');

		// Get tags of interest
		$mt = new MembersTags($database);
		if ($cloud) 
		{
			$tags = $mt->get_tag_cloud(0,0,$juser->get('id'));
		} 
		else 
		{
			$tags = $mt->get_tag_string($juser->get('id'));
		}

		return $tags;
	}

	/**
	 * Retrieves a user's questions
	 * 
	 * @param      string $kind The kind of results to retrieve
	 * @param      array  $interests Array of tags
	 * @return     array  Database results
	 */
	private function _getQuestions($kind='open', $interests=array())
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		// Get some classes we need
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'log.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'questionslog.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'helpers' . DS . 'economy.php');

		$aq = new AnswersTableQuestion($database);
		if ($this->banking) 
		{
			$AE = new AnswersEconomy($database);
			$BT = new Hubzero_Bank_Transaction($database);
		}

		$params =& $this->params;
		$moduleclass = $params->get('moduleclass');
		$limit = intval($params->get('limit'));
		$limit = ($limit) ? $limit : 10;

		$filters = array();
		$filters['limit']    = $limit;
		$filters['start']    = 0;
		$filters['tag']		 = '';
		$filters['filterby'] = 'open';
		$filters['sortby']   = 'date';

		switch ($kind)
		{
			case 'mine':
				$filters['mine'] = 1;
				$filters['sortby'] = 'responses';
		    break;

			case 'assigned':
				$filters['mine'] = 0;
				require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');

				$TA = new ToolAuthor($database);
				$tools = $TA->getToolContributions($juser->get('id'));
				if ($tools) 
				{
					foreach ($tools as $tool)
					{
						$filters['tag'] .= 'tool'.$tool->toolname.',';
					}
				}
				if (!$filters['tag']) 
				{
					$filters['filterby'] = 'none';
				}
		    break;

			case 'interest':
				$filters['mine'] = 0;
				$interests = (count($interests) <= 0) ? $this->_getInterests() : $interests;
				$filters['filterby'] = (!$interests) ? 'none' : 'open';
				$filters['tag'] = $interests;
		    break;
		}

		$results = $aq->getResults($filters);
		if ($this->banking && $results) 
		{
	 		$awards = array();

			foreach ($results as $result)
			{
				// Calculate max award
				$result->marketvalue = round($AE->calculate_marketvalue($result->id, 'maxaward'));
				$result->maxaward = round(2*(($result->marketvalue)/3));
				if ($kind != 'mine') 
				{
					$result->maxaward = $result->maxaward + $result->reward;
				}
				$awards[] = ($result->maxaward) ? $result->maxaward : 0;
			}

			// re-sort by max reponses
			array_multisort($awards, SORT_DESC, $results);
	 	}

		return $results;
	}

	/**
	 * Queries the database for user's questions and preps any data for display
	 * 
	 * @return     void
	 */
	public function display()
	{
		$upconfig =& JComponentHelper::getParams('com_members');
		$this->banking = $upconfig->get('bankAccounts');
		if ($this->banking) 
		{
			ximport('Hubzero_Bank');
		}

		//$juser =& JFactory::getUser();
		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		// show assigned?
		$show_assigned = intval($this->params->get('show_assigned'));
		$show_assigned = $show_assigned ? $show_assigned : 0;
		$this->show_assigned = $show_assigned;

		// show interests?
		$show_interests = intval($this->params->get('show_interests'));
		$show_interests = $show_interests ? $show_interests : 0;
		$this->show_interests = $show_interests;

		// max num of questions
		$max = intval($this->params->get('max_questions'));
		$max= $max ? $max : 12;
		$c = 1;

		// Build the HTML
		//$foundresults = false;
		$assignedcount = 0;
		$othercount = 0;

		// Get Open Questions User Asked
		$this->openquestions = $this->_getQuestions('mine');
		$opencount = ($this->openquestions) ? count($this->openquestions) : 0;

		// Get Questions related to user contributions
		if ($this->show_assigned) 
		{
			$c++;
			$this->assigned = $this->_getQuestions('assigned');
			$assignedcount = ($this->assigned) ? count($this->assigned) : 0;
		}

		// Get interest tags
		if ($this->show_interests) 
		{
			$c++;
			$this->interests = $this->_getInterests();
			if (!$this->interests) 
			{
				$this->intext = JText::_('MOD_MYQUESTIONS_NA');
			} 
			else 
			{
				$this->intext = $this->_formatTags($this->interests);
			}

			// Get questions of interest
			$this->otherquestions = $this->_getQuestions("interest", $this->interests);
			$othercount = ($this->otherquestions) ? count($this->otherquestions) : 0;
		}

		// Limit number of shown questions
		$totalq = $opencount + $assignedcount + $othercount;
		$limit_mine = $max;
		$breaker = $max/$c;
		$this->limit_mine = ($totalq - $opencount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $opencount);
		$this->limit_assigned = ($totalq - $assignedcount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $assignedcount);
		$this->limit_interest = ($totalq - $othercount) >= $breaker * ($c-1) ? $breaker : $max - ($totalq - $othercount);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

