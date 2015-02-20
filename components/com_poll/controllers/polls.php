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

namespace Components\Poll\Controllers;

use Hubzero\Component\SiteController;
use Components\Poll\Tables\Poll;
use Exception;

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_poll' . DS . 'tables' . DS . 'poll.php');

/**
 * Poll controller
 */
class Polls extends SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('results', 'display');

		parent::execute();
	}

	/**
	 * Method to show the search view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$app = \JFactory::getApplication();

		$document = \JFactory::getDocument();
		$pathway  = $app->getPathway();

		$poll_id = \JRequest::getVar('id', 0, '', 'int');

		$poll = new Poll($this->database);
		$poll->load($poll_id);

		// if id value is passed and poll not published then exit
		if ($poll->id > 0 && $poll->published != 1)
		{
			throw new Exception(\JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
		}

		// Adds parameter handling
		$params = $app->getParams();

		// Set page title information
		$menus = $app->getMenu();
		$menu  = $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new \JRegistry($menu->params);
			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', $poll->title);
			}
		}
		else
		{
			$params->set('page_title', $poll->title);
		}
		$document->setTitle($params->get('page_title'));

		//Set pathway information
		$pathway->addItem($poll->title, '');

		$params->def('show_page_title', 1);
		$params->def('page_title', $poll->title);

		$first_vote = '';
		$last_vote  = '';
		$votes      = '';

		// Check if there is a poll corresponding to id and if poll is published
		if ($poll->id > 0)
		{
			if (empty($poll->title))
			{
				$poll->id = 0;
				$poll->title = \JText::_('COM_POLL_SELECT_POLL');
			}

			$query = 'SELECT MIN(date) AS mindate, MAX(date) AS maxdate'
				. ' FROM #__poll_date'
				. ' WHERE poll_id = '. (int) $poll->id;
			$this->database->setQuery($query);
			$dates = $this->database->loadObject();

			if (isset($dates->mindate))
			{
				$first_vote = \JHTML::_('date', $dates->mindate, \JText::_('DATE_FORMAT_LC2'));
				$last_vote  = \JHTML::_('date', $dates->maxdate, \JText::_('DATE_FORMAT_LC2'));
			}

			$query = 'SELECT a.id, a.text, a.hits, b.voters '
				. ' FROM #__poll_data AS a'
				. ' INNER JOIN #__polls AS b ON b.id = a.pollid'
				. ' WHERE a.pollid = '. (int) $poll->id
				. ' AND a.text <> ""'
				. ' ORDER BY a.hits DESC';
			$this->database->setQuery($query);
			$votes = $this->database->loadObjectList();
		}
		else
		{
			$votes = array();
		}

		// list of polls for dropdown selection
		$query = 'SELECT id, title, alias'
			. ' FROM #__polls'
			. ' WHERE published = 1'
			. ' ORDER BY id'
		;
		$this->database->setQuery($query);
		$pList = $this->database->loadObjectList();

		foreach ($pList as $k=>$p)
		{
			$pList[$k]->url = \JRoute::_('index.php?option=com_poll&id=' . $p->id . ':' . $p->alias);
		}

		array_unshift($pList, \JHTML::_('select.option', '', \JText::_('COM_POLL_SELECT_POLL'), 'url', 'title'));

		// dropdown output
		$lists = array();

		$lists['polls'] = \JHTML::_('select.genericlist', $pList, 'id',
			'class="inputbox" size="1" style="width:200px" onchange="if (this.options[selectedIndex].value != \'\') {document.location.href=this.options[selectedIndex].value}"',
			'url', 'title',
			\JRoute::_('index.php?option=com_poll&id=' . $poll->id . ':' . $poll->alias)
		);


		$graphwidth = 200;
		$barheight  = 4;
		$maxcolors  = 5;
		$barcolor   = 0;
		$tabcnt     = 0;
		$colorx     = 0;

		$maxval = isset($votes[0]) ? $votes[0]->hits : 0;
		$sumval = isset($votes[0]) ? $votes[0]->voters : 0;

		$k = 0;
		for ($i = 0; $i < count($votes); $i++)
		{
			$vote =& $votes[$i];

			if ($maxval > 0 && $sumval > 0)
			{
				$vote->width   = ceil($vote->hits * $graphwidth / $maxval);
				$vote->percent = round(100 * $vote->hits / $sumval, 1);
			}
			else
			{
				$vote->width   = 0;
				$vote->percent = 0;
			}

			$vote->class = '';
			if ($barcolor == 0)
			{
				if ($colorx < $maxcolors)
				{
					$colorx = ++$colorx;
				}
				else
				{
					$colorx = 1;
				}
				$vote->class = "polls_color_" . $colorx;
			}
			else
			{
				$vote->class = "polls_color_" . $barcolor;
			}

			$vote->barheight = $barheight;

			$vote->odd   = $k;
			$vote->count = $i;
			$k = 1 - $k;
		}

		$this->view
			->set('first_vote', $first_vote)
			->set('last_vote', $last_vote)
			->set('lists', $lists)
			->set('params', $params)
			->set('poll', $poll)
			->set('votes', $votes)
			->setLayout('default')
			->display();
	}

	/**
	 * Method to show the latest poll
	 *
	 * @return  void
	 */
	public function latestTask()
	{
		$app      = \JFactory::getApplication();
		$document = \JFactory::getDocument();
		$pathway  = $app->getPathway();

		$model = new Poll($this->database);
		$poll = $model->getLatest();

		// if id value is passed and poll not published then exit
		if ($poll->id > 0 && $poll->published != 1)
		{
			throw new Exception(\JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
		}

		$options = $model->getPollOptions($poll->id);

		// Adds parameter handling
		$params = $app->getParams();

		//Set page title information
		$menus = $app->getMenu();
		$menu  = $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new \JRegistry($menu->params);
			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', $poll->title);
			}
		}
		else
		{
			$params->set('page_title', $poll->title);
		}
		$document->setTitle($params->get('page_title'));

		//Set pathway information
		$pathway->addItem($poll->title, '');

		$params->def('show_page_title', 1);
		$params->def('page_title', $poll->title);

		$this->view
			->set('options', $options)
			->set('params', $params)
			->set('poll', $poll)
			->display();
	}

	/**
 	 * Add a vote to an option
 	 */
	public function voteTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		$poll_id   = \JRequest::getVar('id', 0, '', 'int');
		$option_id = \JRequest::getVar('voteid', 0, 'post', 'int');

		$poll = new Poll($this->database);
		if (!$poll->load($poll_id) || $poll->published != 1)
		{
			throw new Exception(\JText::_('JERROR_ALERTNOAUTHOR'), 404);
		}

		$app = \JFactory::getApplication();

		$cookieName = \JUtility::getHash($app->getName() . 'poll' . $poll_id);

		// ToDo - may be adding those information to the session?
		$voted = \JRequest::getVar($cookieName, '0', 'COOKIE', 'INT');

		if ($voted || !$option_id)
		{
			if ($voted)
			{
				$msg = \JText::_('COM_POLL_ALREADY_VOTED');
			}

			if (!$option_id)
			{
				$msg = \JText::_('COM_POLL_WARNSELECT');
			}
		}
		else
		{
			setcookie($cookieName, '1', time() + $poll->lag);

			$poll->vote($poll_id, $option_id);

			$msg = \JText::_('COM_POLL_THANK_YOU');
		}

		// set Itemid id for links
		$menu   = $app->getMenu();
		$items  = $menu->getItems('link', 'index.php?option=com_poll&view=poll');
		$itemid = isset($items[0]) ? '&Itemid=' . $items[0]->id : '';

		$this->setRedirect(
			\JRoute::_('index.php?option=com_poll&id=' . $poll_id . ':' . $poll->alias . $itemid, false),
			$msg
		);
	}
}
