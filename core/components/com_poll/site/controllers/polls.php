<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Poll\Models\Poll;
use Document;
use Request;
use Pathway;
use Notify;
use Route;
use Lang;
use Html;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'poll.php');

/**
 * Poll controller
 */
class Polls extends SiteController
{
	/**
	 * Method to show the search view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		Document::setTitle(Lang::txt('COM_POLL'));

		//Set pathway information
		Pathway::append(Lang::txt('COM_POLL'), Route::url('index.php?option=' . $this->_option));

		$polls = Poll::all()
			->whereEquals('state', 1)
			->rows();

		$this->view
			->set('polls', $polls)
			->display();
	}

	/**
	 * Method to show the search view
	 *
	 * @return  void
	 */
	public function resultsTask()
	{
		$poll_id = Request::getInt('id', 0);

		$poll = Poll::oneOrFail($poll_id);

		// if id value is passed and poll not published then exit
		if ($poll->get('id') && $poll->get('state') != 1)
		{
			App::abort(403, Lang::txt('JGLOBAL_AUTH_ACCESS_DENIED'));
		}

		// Adds parameter handling
		$params = App::get('menu.params');

		// Set page title information
		$menus = App::get('menu');
		$menu  = $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new \Hubzero\Config\Registry($menu->params);
			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', $poll->title);
			}
		}
		else
		{
			$params->set('page_title', $poll->title);
		}

		Document::setTitle($params->get('page_title'));

		//Set pathway information
		Pathway::append(
			Lang::txt('COM_POLL'),
			Route::url('index.php?option=' . $this->_option)
		);
		Pathway::append(
			$poll->get('title'),
			Route::url('index.php?option=' . $this->_option . '&id=' . $poll->get('id') . ':' . $poll->get('alias'))
		);

		$params->def('show_page_title', 1);
		$params->def('page_title', $poll->get('title'));

		$first_vote = '';
		$last_vote  = '';
		$votes      = array();

		// Check if there is a poll corresponding to id and if poll is published
		if ($poll->get('id'))
		{
			$dates = $poll->dates()
				->select('MIN(date)', 'mindate')
				->select('MAX(date)', 'maxdate')
				->row();

			if ($dates->get('mindate'))
			{
				$first_vote = \Date::of($dates->get('mindate'))->toLocal(Lang::txt('DATE_FORMAT_LC2'));
				$last_vote  = \Date::of($dates->get('maxdate'))->toLocal(Lang::txt('DATE_FORMAT_LC2'));
			}

			$votes = $poll->options()
				->where('text', '!=', '')
				->order('hits', 'desc')
				->ordered()
				->rows()
				->raw();

			$votes = array_values($votes);
		}

		// list of polls for dropdown selection
		$ps = Poll::all()
			->whereEquals('state', 1)
			->rows()
			->raw();

		$pList = array();
		foreach ($ps as $k => $p)
		{
			$pList[$k] = $p;
			$pList[$k]->set('url', Route::url('index.php?option=com_poll&id=' . $p->get('id') . ':' . $p->get('alias')));
		}

		array_unshift($pList, Html::select('option', '', Lang::txt('COM_POLL_SELECT_POLL'), 'url', 'title'));

		// dropdown output
		$lists = array(
			'polls' => Html::select('genericlist', $pList, 'id',
				'class="inputbox" size="1" onchange="if (this.options[selectedIndex].value != \'\') {document.location.href=this.options[selectedIndex].value}"',
				'url', 'title',
				Route::url('index.php?option=com_poll&id=' . $poll->get('id') . ':' . $poll->get('alias'))
			)
		);

		$graphwidth = 200;
		$barheight  = 4;
		$maxcolors  = 5;
		$barcolor   = 0;
		$tabcnt     = 0;
		$colorx     = 0;

		$maxval = isset($votes[0]) ? $votes[0]->hits : 0;
		$sumval = $poll->voters; //isset($votes[0]) ? $votes[0]->voters : 0;

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
				$vote->class = 'polls_color_' . $colorx;
			}
			else
			{
				$vote->class = 'polls_color_' . $barcolor;
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
			->display();
	}

	/**
	 * Method to show the latest poll
	 *
	 * @return  void
	 */
	public function latestTask()
	{
		$poll = Poll::current();

		// if id value is passed and poll not published then exit
		if ($poll->get('id') && $poll->get('state') != 1)
		{
			App::abort(403, Lang::txt('JGLOBAL_AUTH_ACCESS_DENIED'));
		}

		$options = $poll->options()
			->where('text', '!=', '')
			->order('hits', 'desc')
			->ordered()
			->rows();

		// Adds parameter handling
		$params = App::get('menu.params');

		//Set page title information
		$menus = App::get('menu');
		$menu  = $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new \Hubzero\Config\Registry($menu->params);
			if (!$menu_params->get('page_title'))
			{
				$params->set('page_title', $poll->get('title'));
			}
		}
		else
		{
			$params->set('page_title', $poll->get('title'));
		}

		Document::setTitle($params->get('page_title'));

		//Set pathway information
		Pathway::append(
			Lang::txt('COM_POLL'),
			Route::url('index.php?option=' . $this->_option)
		);
		Pathway::append(
			$poll->get('title'),
			Route::url('index.php?option=' . $this->_option . '&id=' . $poll->get('id') . ':' . $poll->get('alias'))
		);

		$params->def('show_page_title', 1);
		$params->def('page_title', $poll->get('title'));

		$this->view
			->set('options', $options)
			->set('params', $params)
			->set('poll', $poll)
			->display();
	}

	/**
 	 * Add a vote to an option
 	 *
 	 * @return  void
 	 */
	public function voteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$poll_id   = Request::getVar('id', 0, '', 'int');
		$option_id = Request::getVar('voteid', 0, 'post', 'int');

		$poll = Poll::oneOrFail($poll_id);

		if ($poll->get('state') != 1)
		{
			App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$cookieName = App::hash(App::get('client')->name . 'poll' . $poll_id);

		// ToDo - may be adding those information to the session?
		$voted = Request::getVar($cookieName, '0', 'COOKIE', 'INT');

		if ($voted || !$option_id)
		{
			if ($voted)
			{
				Notify::warning(Lang::txt('COM_POLL_ALREADY_VOTED'));
			}

			if (!$option_id)
			{
				Notify::warning(Lang::txt('COM_POLL_WARNSELECT'));
			}
		}
		else
		{
			// Determine whether cookie should be 'secure' or not
			$secure   = false;
			$forceSsl = \Config::get('force_ssl', false);

			if (App::isAdmin() && $forceSsl >= 1)
			{
				$secure = true;
			}
			else if (App::isSite() && $forceSsl == 2)
			{
				$secure = true;
			}

			setcookie($cookieName, '1', time() + $poll->get('lag'), '/', '', $secure, true);

			$poll->vote($option_id);

			Notify::success(Lang::txt('COM_POLL_THANK_YOU'));
		}

		// set Itemid id for links
		$menu   = App::get('menu');
		$items  = $menu->getItems('link', 'index.php?option=com_poll&view=poll');
		$itemid = isset($items[0]) ? '&Itemid=' . $items[0]->id : '';

		App::redirect(
			Route::url('index.php?option=com_poll&id=' . $poll_id . ':' . $poll->get('alias') . $itemid, false)
		);
	}
}
