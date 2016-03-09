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

use Components\Feedaggregator\Models\Feed;
use Hubzero\Component\SiteController;
use Request;
use Notify;
use Route;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'feed.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'post.php');

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
		if (User::isGuest()) // have person login
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
				'warning'
			);
		}

		$authlevel = User::getAuthorisedViewLevels();
		$access_level = 3; //author_level

		if (!in_array($access_level, $authlevel))
		{
			App::redirect(
				Route::url('index.php?option=com_feedaggregator'),
				Lang::txt('COM_FEEDAGGREGATOR_NOT_AUTH'),
				'warning'
			);
		}

		$feeds = Feed::all()
			->rows();

		$this->view
			->set('title', Lang::txt('COM_FEEDAGGREGATOR'))
			->set('feeds', $feeds)
			->display();
	}

	/**
	 * Edit source feed form, load appropriate record
	 *
	 * @param   object  $feed
	 * @return  void
	 */
	public function editTask($feed = null)
	{
		if (!is_object($feed))
		{
			$feed = Feed::oneOrNew(Request::getInt('id', 0));
		}

		$this->view
			->set('title', ($this->getTask() == 'new' ? Lang::txt('COM_FEEDAGGREGATOR_ADD_FEED') : Lang::txt('COM_FEEDAGGREGATOR_EDIT_FEEDS')))
			->set('feed', $feed)
			->setLayout('edit')
			->display();
	}

	/**
	 * Displays empty form for adding source feed
	 *
	 * @return  void
	 */
	public function newTask()
	{
		return $this->editTask();
	}

	/**
	 * Enables or disables a source feed
	 *
	 * @return  void
	 */
	public function statusTask()
	{
		$id     = Request::getInt('id');
		$action = Request::getVar('action');

		$enabled = ($action == 'enable' ? 1 : 0);

		$model = Feed::oneOrFail($id);
		$model->set('enabled', $enabled);

		if (!$model->save())
		{
			Notify::error(Lang::txt('COM_FEEDAGGREGATOR_ERROR_ENABLE_DISABLE_FAILED'));
		}
		else
		{
			Notify::success(
				$enabled ? Lang::txt('COM_FEEDAGGREGATOR_FEED_ENABLED') : Lang::txt('COM_FEEDAGGREGATOR_FEED_DISABLED')
			);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
		);
	}

	/**
	 * Save Source Feed form
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// get the URL first in order to validate
		$feed = Feed::blank();
		$feed->set('id', Request::getVar('id'));
		$feed->set('url', Request::getVar('url'));
		$feed->set('name', Request::getVar('name'));
		$feed->set('enabled', Request::getVar('enabled'));
		$feed->set('description', Request::getVar('description'));

		//validate url
		if (!filter_var($feed->get('url'), FILTER_VALIDATE_URL))
		{
			Notify::error(Lang::txt('COM_FEEDAGGREGATOR_ERROR_INVALID_URL'));
			return $this->editTask($feed);
		}

		if (!$feed->save())
		{
			Notify::error(Lang::txt('COM_FEEDAGGREGATOR_ERROR_UPDATE_FAILED'));
			return $this->editTask($feed);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller),
			Lang::txt('COM_FEEDAGGREGATOR_INFORMATION_UPDATED')
		);
	}
}
