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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedaggregator\Site\Controllers;

use Components\Feedaggregator\Models;
use Hubzero\Component\SiteController;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'feeds.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'posts.php');

/**
 *  Feed Aggregator controller class
 */
class Feeds extends SiteController
{
	/**
	 * Default component view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$authlevel = \JAccess::getAuthorisedViewLevels(User::get('id'));
		$access_level = 3; //author_level

		if (in_array($access_level, $authlevel) && User::get('id'))
		{
			$model = new Models\Feeds;

			$this->view->feeds = $model->loadAll();
			$this->view->title = Lang::txt('COM_FEEDAGGREGATOR');
			$this->view->display();
		}
		else if (User::get('id'))
		{
			App::redirect(
				Route::url('index.php?option=com_feedaggregator'),
				Lang::txt('COM_FEEDAGGREGATOR_NOT_AUTH'),
				'warning'
			);
		}
		else if (User::isguest()) // have person login
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
				'warning'
			);
		}
	}

	/**
	 * Edit source feed form, load appropriate record
	 *
	 * @return  void
	 */
	public function editTask()
	{
		//isset ID kinda deal
		$model = new Models\Feeds;

		$this->view->feed  = $model->loadbyId(Request::getInt('id', 0));
		$this->view->user  = User::getRoot();
		$this->view->title = Lang::txt('COM_FEEDAGGREGATOR_EDIT_FEEDS');
		$this->view->display();
	}

	/**
	 * Displays empty form for adding source feed
	 *
	 * @return  void
	 */
	public function newTask()
	{
		$this->view
			->set('title', Lang::txt('COM_FEEDAGGREGATOR_ADD_FEED'))
			->setLayout('edit')
			->display();
	}

	/**
	 * Enables or disables a source feed
	 *
	 * @return  void
	 */
	public function statusTask()
	{
		$id = Request::getInt('id');
		$action = Request::getVar('action');
		$model = new Models\Feeds();

		if ($action == 'enable')
		{
			$model->updateActive($id, 1);
			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_FEEDAGGREGATOR_FEED_ENABLED')
			);
		}
		elseif ($action == 'disable')
		{
			$model->updateActive($id, 0);

			// Output messsage and redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_FEEDAGGREGATOR_FEED_DISABLED')
			);
		}
		else
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
				Lang::txt('COM_FEEDAGGREGATOR_ERROR_ENABLE_DISABLE_FAILED'),
				'error'
			);
		}
	}

	/**
	 * Save Source Feed form
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		//do a Request instead of a bind()
		$feed = new Models\Feeds;

		//get the URL first in order to validate
		$feed->set('url', Request::getVar('url'));
		$feed->set('name', Request::getVar('name'));
		$feed->set('id', Request::getVar('id'));
		$feed->set('enabled', Request::getVar('enabled'));
		$feed->set('description', Request::getVar('description'));

		//validate url
		if (!filter_var($feed->get('url'), FILTER_VALIDATE_URL))
		{
			$this->feed = $feed;

			//redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=feeds&task=new'),
				Lang::txt('COM_FEEDAGGREGATOR_ERROR_INVALID_URL'),
				'warning'
			);
		}
		else
		{
			if ($feed->store())
			{
				// Output messsage and redirect
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					Lang::txt('COM_FEEDAGGREGATOR_INFORMATION_UPDATED')
				);
			}
			else
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
					Lang::txt('COM_FEEDAGGREGATOR_ERROR_UPDATE_FAILED'),
					'warning'
				);
			}
		}
	}
}